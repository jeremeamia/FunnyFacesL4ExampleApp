<?php

use Aws\Common\Aws;
use Aws\DynamoDb\Iterator\ItemIterator;
use Symfony\Component\HttpFoundation\File\File;

class FunnyFace
{
    /** @var Aws */
    private $sdk;

    /** @var array */
    private $config;

    /**
     * @param Aws   $sdk
     * @param array $config
     */
    public function __construct(Aws $sdk, array $config)
    {
        $this->sdk = $sdk;
        $this->config = $config;
    }

    /**
     * @param int $limit
     *
     * @return ItemIterator
     */
    public function latest($limit = 25)
    {
        $items = $this->sdk->get('dynamodb')->getIterator('Query', [
            'TableName' => $this->config['table'],
            'ScanIndexForward' => false,
            'Limit' => $limit,
            'KeyConditions' => [
                'app' => [
                    'AttributeValueList' => [['S' => $this->config['hashkey']]],
                    'ComparisonOperator' => 'EQ',
                ],
                'time' => [
                    'AttributeValueList' => [['N' => (string) strtotime('-1 month')]],
                    'ComparisonOperator' => 'GT',
                ]
            ]
        ]);

        return new ItemIterator($items);
    }

    /**
     * @param File   $file
     * @param string $caption
     */
    public function add(File $file, $caption)
    {
        $url = $this->sdk->get('s3')->putObject([
            'Bucket' => $this->config['bucket'],
            'Key'    => $file->getFileName(),
            'Body'   => fopen($file->getPathname(), 'r'),
            'ACL'    => 'public-read',
        ])['ObjectURL'];

        $this->sdk->get('dynamodb')->putItem([
            'TableName' => $this->config['table'],
            'Item' => [
                'app'     => ['S' => $this->config['hashkey']],
                'time'    => ['N' => (string) time()],
                'src'     => ['S' => $url],
                'caption' => ['S' => $caption],
            ],
        ]);
    }
}
