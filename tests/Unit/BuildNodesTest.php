<?php


namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Utilities\TreeBuilder;

class BuildNodesTest extends TestCase
{
    public function testEmptyInput(): void
    {
        $this->assertEquals([], TreeBuilder::buildNodes([]));
    }

    public function testSingleFile(): void
    {
        $items = [['path' => 'file.txt', 'type' => 'blob', 'sha' => '123', 'size' => 10]];
        $expected = [
            [
                'key' => 'file.txt:123',
                'data' => ['name' => 'file.txt', 'type' => 'File', 'size' => 10]
            ]
        ];
        $this->assertEquals($expected, TreeBuilder::buildNodes($items));
    }

    public function testSingleFolderAndFile(): void
    {
        $items = [
            ['path' => 'folder/file.txt', 'type' => 'blob', 'sha' => '123', 'size' => 10],
        ];

        $result = TreeBuilder::buildNodes($items);
        $this->assertCount(1, $result);
        $this->assertEquals('folder', $result[0]['data']['name']);
        $this->assertCount(1, $result[0]['children']);
        $this->assertEquals('file.txt', $result[0]['children'][0]['data']['name']);
    }

    public function testMultipleFilesAndFolders(): void
    {
        $items = [
            ['path' => 'folder1/file1.txt', 'type' => 'blob', 'sha' => '123', 'size' => 10],
            ['path' => 'folder1/file2.txt', 'type' => 'blob', 'sha' => '456', 'size' => 20],
            ['path' => 'folder2/file3.txt', 'type' => 'blob', 'sha' => '789', 'size' => 30],
        ];

        $result = TreeBuilder::buildNodes($items);
        $this->assertCount(2, $result);
        $this->assertEquals('folder1', $result[0]['data']['name']);
        $this->assertEquals('folder2', $result[1]['data']['name']);
        $this->assertCount(2, $result[0]['children']);
        $this->assertCount(1, $result[1]['children']);
    }

    public function testFolderType(): void
    {
        $items = [
            ['path' => 'folder1', 'type' => 'tree', 'sha' => '123', 'size' => 10],
        ];

        $result = TreeBuilder::buildNodes($items);
        $this->assertCount(1, $result);
        $this->assertEquals('folder1', $result[0]['data']['name']);
        $this->assertEquals('Folder', $result[0]['data']['type']);
    }
}
