<?php

namespace NSWDPC\Elemental\Models\Mediawesome\Tests;

use NSWDPC\GridHelper\Extensions\ElementChildGridExtension;
use NSWDPC\GridHelper\Models\Configuration;
use NSWDPC\Elemental\Models\Mediawesome\ElementMediawesome;
use SilverStripe\Core\Config\Config;
use SilverStripe\Dev\SapphireTest;
use nglasl\mediawesome\MediaTag;
use nglasl\mediawesome\MediaHolder;
use nglasl\mediawesome\MediaPage;

class ElementMediawesomeTest extends SapphireTest {

    protected $usesDatabase =  true;

    protected static $fixture_file = "./ElementMediawesomeTest.yml";

    public function testColumnCount() {

        Config::modify()->set( Configuration::class, 'grid_prefix', 'test-col');
        $element = ElementMediawesome::create();
        $element->CardColumns = 3;

        $this->assertEquals(
            "test-col-xs-12 test-col-sm-6 test-col-md-4 test-col-lg-4",
            $element->ColumnClass()
        );

    }

    public function testRecentPosts() {
        MediaPage::create()->requireDefaultRecords();//setup
        $holder = $this->objFromFixture( MediaHolder::class, 'holder1');
        $element = ElementMediawesome::create();
        $element->MediaHolderID = $holder->ID;
        $element->NumberOfPosts = 3;
        $recentPosts = $element->getRecentPosts();
        $this->assertEquals(3, $recentPosts->count());
    }

    public function testRecentPostsNoHolder() {
        MediaPage::create()->requireDefaultRecords();//setup
        $holder = $this->objFromFixture( MediaHolder::class, 'holder1');
        $element = ElementMediawesome::create();
        $element->NumberOfPosts = 3;
        $recentPosts = $element->getRecentPosts();
        $this->assertNull($recentPosts);
    }

    public function testRecentPostsWithTag() {
        MediaPage::create()->requireDefaultRecords();//setup
        $holder = $this->objFromFixture( MediaHolder::class, 'holder1');
        $tag = $this->objFromFixture( MediaTag::class, 'tag1' );
        $element = ElementMediawesome::create();
        $element->MediaHolderID = $holder->ID;
        $element->NumberOfPosts = 3;
        $element->TagID = $tag->ID;
        $recentPosts = $element->getRecentPosts();
        $this->assertEquals(1, $recentPosts->count());
        $this->assertEquals('PageWithTags', $recentPosts->first()->Title);
    }

    public function testRecentPostsWithNoLimit() {
        MediaPage::create()->requireDefaultRecords();//setup
        $holder = $this->objFromFixture( MediaHolder::class, 'holder1');
        $element = ElementMediawesome::create();
        $element->MediaHolderID = $holder->ID;
        $element->NumberOfPosts = 0;
        $recentPosts = $element->getRecentPosts();
        // no limit
        $this->assertEquals(4, $recentPosts->count());
    }

}
