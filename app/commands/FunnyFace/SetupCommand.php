<?php namespace FunnyFace;

use Aws\Common\Exception\AwsExceptionInterface;
use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Exception\ResourceInUseException;
use Aws\S3\S3Client;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class SetupCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'funnyface:setup';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Setup the Funny Face App.';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		// Check if the config file has been written
		$path = $this->laravel['path.base'] . '/app/config/funnyfaces.php';
		if (file_exists($path)) {
			$this->info("The The Funny Face App is already setup.");
			return;
		}

		// Create a config file with the bucket and table
		$config = $this->getConfigData();
		$this->writeConfigFile($config, $path);
		$this->info("A config file has been written to app/config/funnyfaces.php.");

		$aws = $this->laravel['aws'];
		try {
			$this->info("Creating an S3 bucket \"{$config['bucket']}\"...");
			if (!$this->createBucket($aws->get('s3'), $config['bucket'])) {
				$this->info("Done.");
			} else {
				$this->info("The S3 bucket \"{$config['bucket']}\" already exists.");
			}

			$this->info("Creating a DynamoDB table \"{$config['table']}\"...");
			$this->createTable($aws->get('dynamodb'), $config['table']);
			$this->info("Done.");
		} catch (ResourceInUseException $e) {
			$this->info("The DynamoDB table \"{$config['table']}\" already exists.");
		} catch (AwsExceptionInterface $e) {
			$this->error("There was an error during setup. {$e}");
			$this->info("Initiating teardown...");
			$this->call('funnyface:teardown');
			$this->error("Setup failed.");
			return;
		}

		$this->info("The Funny Face App is setup.");
	}

	protected function getConfigData()
	{
		$suffix = strtolower(str_random(12));

		return [
			'hashkey' => $this->input->getOption('hashkey') ?: $suffix,
			'bucket'  => $this->input->getOption('bucket') ?: "funnyfaces-{$suffix}",
			'table'   => $this->input->getOption('table') ?: "funnyfaces-{$suffix}",
		];
	}

	protected function getOptions()
	{
		return [
			['bucket',  null, InputOption::VALUE_OPTIONAL, 'S3 Bucket for storing images.'],
			['table',   null, InputOption::VALUE_OPTIONAL, 'DynamoDB Table for storing funny face data.'],
			['hashkey', null, InputOption::VALUE_OPTIONAL, 'HashKey for DynamoDB table.'],
		];
	}

	private function createBucket(S3Client $s3, $bucket)
	{
		if (!$s3->doesBucketExist($bucket)) {
			$s3->createBucket(['Bucket' => $bucket]);
			$s3->waitUntil('BucketExists', ['Bucket' => $bucket]);
			return true;
		}

		return false;
	}

	private function createTable(DynamoDbClient $db, $table)
	{
		$db->createTable([
			'TableName' => $table,
			'AttributeDefinitions' => [
				['AttributeName' => 'app',  'AttributeType' => 'S'],
				['AttributeName' => 'time', 'AttributeType' => 'N'],
			],
			'KeySchema' => [
				['AttributeName' => 'app',  'KeyType' => 'HASH'],
				['AttributeName' => 'time', 'KeyType' => 'RANGE'],
			],
			'ProvisionedThroughput' => [
				'ReadCapacityUnits'  => 5,
				'WriteCapacityUnits' => 1,
			],
		]);
		$db->waitUntil('TableExists', ['TableName' => $table]);
	}

	private function writeConfigFile(array $config, $path)
	{
		$content = sprintf("<?php return %s;\n", var_export($config, true));

		return (bool) file_put_contents($path, $content);
	}

}
