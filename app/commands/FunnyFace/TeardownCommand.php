<?php namespace FunnyFace;

use Aws\S3\S3Client;
use Aws\Common\Exception\AwsExceptionInterface;
use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Exception\ResourceNotFoundException;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class TeardownCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'funnyface:teardown';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Teardown the Funny Face App.';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		// Check if the config file exists
		$path = $this->laravel['path.base'] . '/app/config/funnyfaces.php';
		if (!file_exists($path)) {
			$this->error('Could not find the config file. Are you sure that you setup the The Funny Face App?');
			return;
		}

		$config = $this->getConfigData();
		$aws = $this->laravel['aws'];

		try {
			$this->info("Deleting the S3 bucket \"{$config['bucket']}\"...");
			if ($this->deleteBucket($aws->get('s3'), $config['bucket'])) {
				$this->info("Done.");
			} else {
				$this->info("S3 bucket \"{$config['bucket']}\" was already deleted.");
			}

			$this->info("Deleting the DynamoDB table \"{$config['table']}\"...");
			$this->deleteTable($aws->get('dynamodb'), $config['table']);
			$this->info("Done.");
		} catch (ResourceNotFoundException $e) {
			$this->info("DynamoDB table \"{$config['table']}\" was already deleted.");
		} catch (AwsExceptionInterface $e) {
			$this->error($e);
		}

		unlink($path);
		$this->info("The app/config/funnyfaces.php config file has been deleted.");

		$this->info("The Funny Face App is now torn down.");
	}

	protected function getConfigData()
	{
		return $this->laravel['config']->get('funnyfaces');
	}

	protected function getOptions()
	{
		return [
			['bucket', null, InputOption::VALUE_OPTIONAL, 'S3 Bucket for storing images.'],
			['table',  null, InputOption::VALUE_OPTIONAL, 'DynamoDB Table for storing funny face data.'],
		];
	}

	private function deleteBucket(S3Client $s3, $bucket)
	{
		if ($s3->doesBucketExist($bucket)) {
			$s3->clearBucket($bucket);
			$s3->deleteBucket(['Bucket' => $bucket]);
			$s3->waitUntil('BucketNotExists', ['Bucket' => $bucket]);

			return true;
		}

		return false;
	}

	private function deleteTable(DynamoDbClient $db, $table)
	{
		$db->deleteTable(['TableName' => $table]);
		$db->waitUntil('TableNotExists', ['TableName' => $table]);
	}
}
