<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/3/24
 * Time: 下午10:05
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Protocol\Http\Tests\Upload;

use FastD\Http\File\Upload;

class UploaderTest extends \PHPUnit_Framework_TestCase
{
    protected $one = [];

    protected $multi = [];

    public function setUp()
    {
        $this->one = [
            'file' => [
                'name' => 'test.jpg',
                'type' => 'image/jpeg',
                'size' => 0,
                'tmp_name' => __DIR__ . '/tmp/test.jpg',
                'error' => 0
            ]
        ];
    }

    public function testUploadOne()
    {
        $upload = new Upload($this->one);

        print_r($upload);
    }
}
