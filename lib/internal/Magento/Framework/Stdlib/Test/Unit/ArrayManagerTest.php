<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Stdlib\Test\Unit;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Framework\Stdlib\ArrayManager;

class ArrayManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->arrayManager = $this->objectManagerHelper->getObject(ArrayManager::class);
    }

    /**
     * @param string $path
     * @param array $data
     * @param bool $result
     * @dataProvider existsDataProvider
     */
    public function testExists($path, $data, $result)
    {
        $this->assertSame($result, $this->arrayManager->exists($path, $data));
    }

    /**
     * @return array
     */
    public function existsDataProvider()
    {
        return [
            0 => [
                'path' => 'some/path',
                'data' => ['some' => ['path' => null]],
                'result' => true
            ],
            1 => [
                'path' => '0/0/test',
                'data' => [[['test' => false]]],
                'result' => true
            ],
            2 => [
                'path' => 'invalid/path',
                'data' => ['valid' => ['path' => 0]],
                'result' => false
            ]
        ];
    }

    public function testExistsCustomDelimiter()
    {
        $data = ['custom' => ['delimiter' => null]];

        $this->assertFalse($this->arrayManager->exists('custom/delimiter', $data, '~'));
        $this->assertTrue($this->arrayManager->exists('custom~delimiter', $data, '~'));
    }

    /**
     * @param string $path
     * @param array $data
     * @param mixed $result
     * @dataProvider getDataProvider
     */
    public function testGet($path, $data, $result)
    {
        $this->assertSame($result, $this->arrayManager->get($path, $data));
    }

    /**
     * @return array
     */
    public function getDataProvider()
    {
        return [
            0 => [
                'path' => 'nested/path/0',
                'data' => ['nested' => ['path' => ['value1']]],
                'result' => 'value1'
            ],
            1 => [
                'path' => '0',
                'data' => [false],
                'result' => false
            ],
            2 => [
                'path' => 'invalid/path/0',
                'data' => [],
                'result' => null
            ]
        ];
    }

    /**
     * @param string $path
     * @param array $data
     * @param mixed $value
     * @param array $result
     * @dataProvider setDataProvider
     */
    public function testSet($path, $data, $value, $result)
    {
        $this->assertSame($result, $this->arrayManager->set($path, $data, $value));
    }

    /**
     * @return array
     */
    public function setDataProvider()
    {
        return [
            0 => [
                'path' => '0/1',
                'data' => [[false, false]],
                'value' => true,
                'result' => [[false, true]]
            ],
            1 => [
                'path' => 'test',
                'data' => ['test' => ['lost data']],
                'value' => 'found data',
                'result' => ['test' => 'found data']
            ],
            2 => [
                'path' => 'new/path/2',
                'data' => ['existing' => ['path' => 1]],
                'value' => 'valuable data',
                'result' => ['existing' => ['path' => 1], 'new' => ['path' => [2 => 'valuable data']]]
            ]
        ];
    }

    /**
     * @param string $path
     * @param array $data
     * @param mixed $value
     * @param array $result
     * @dataProvider setDataProvider
     */
    public function testReplace($path, $data, $value, $result)
    {
        $this->assertSame($result, $this->arrayManager->set($path, $data, $value));
    }

    /**
     * @return array
     */
    public function setReplaceProvider()
    {
        return [
            0 => [
                'path' => '0/1',
                'data' => [[false, false]],
                'value' => true,
                'result' => [[false, true]]
            ],
            1 => [
                'path' => 'test',
                'data' => ['test' => ['lost data']],
                'value' => 'found data',
                'result' => ['test' => 'found data']
            ],
            2 => [
                'path' => 'new/path/2',
                'data' => ['existing' => ['path' => 1]],
                'value' => 'valuable data',
                'result' => ['existing' => ['path' => 1]]
            ]
        ];
    }

    /**
     * @param string $path
     * @param array $data
     * @param array $value
     * @param array $result
     * @dataProvider mergeDataProvider
     */
    public function testMerge($path, $data, $value, $result)
    {
        $this->assertSame($result, $this->arrayManager->merge($path, $data, $value));
    }

    /**
     * @return array
     */
    public function mergeDataProvider()
    {
        return [
            0 => [
                'path' => '0/path/1',
                'data' => [['path' => [false, ['value' => false]]]],
                'value' => ['value' => true, 'new_value' => false],
                'result' => [['path' => [false, ['value' => true, 'new_value' => false]]]]
            ],
            1 => [
                'path' => 0,
                'data' => [['nested' => ['test' => 2, 'test2' => 1]]],
                'value' => ['nested' => ['test' => 3], 'more' => 4],
                'result' => [['nested' => ['test' => 3, 'test2' => 1], 'more' => 4]]
            ],
            2 => [
                'path' => 'invalid/path',
                'data' => [],
                'value' => [true],
                'result' => []
            ]
        ];
    }

    /**
     * @param string $path
     * @param array $data
     * @param array $result
     * @dataProvider removeDataProvider
     */
    public function testRemove($path, $data, $result)
    {
        $this->assertSame($result, $this->arrayManager->remove($path, $data));
    }

    /**
     * @return array
     */
    public function removeDataProvider()
    {
        return [
            0 => [
                'path' => '0/0/0/0',
                'data' => [[[[null]]]],
                'result' => [[[[]]]]
            ],
            1 => [
                'path' => 'simple',
                'data' => ['simple' => true, 'complex' => false],
                'result' => ['complex' => false]
            ],
            2 => [
                'path' => 'invalid',
                'data' => [true],
                'result' => [true]
            ]
        ];
    }

    /**
     * @param string $path
     * @param int $offset
     * @param int|null $length
     * @param string $result
     * @dataProvider slicePathDataProvider
     */
    public function testSlicePath($path, $offset, $length, $result)
    {
        $this->assertSame($result, $this->arrayManager->slicePath($path, $offset, $length));
    }

    /**
     * @return array
     */
    public function slicePathDataProvider()
    {
        $path = 'some/very/very/long/path/0/goes/1/3/here';

        return [
            0 => [
                'path' => $path,
                'offset' => 3,
                'length' => null,
                'result' => 'long/path/0/goes/1/3/here'
            ],
            1 => [
                'path' => $path,
                'offset' => -3,
                'length' => null,
                'result' => '1/3/here'
            ],
            2 => [
                'path' => $path,
                'offset' => 500,
                'length' => null,
                'result' => ''
            ],
            3 => [
                'path' => $path,
                'offset' => 2,
                'length' => 2,
                'result' => 'very/long'
            ],
            4 => [
                'path' => $path,
                'offset' => -6,
                'length' => 3,
                'result' => 'path/0/goes'
            ]
        ];
    }

    public function testSlicePathCustomDelimiter()
    {
        $path = 'my~custom~path';

        $this->assertSame('custom', $this->arrayManager->slicePath($path, 1, 1, '~'));
        $this->assertSame('', $this->arrayManager->slicePath($path, 1, 1));
    }
}
