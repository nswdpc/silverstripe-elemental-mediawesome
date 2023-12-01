<?php
namespace NSWDPC\Elemental\Models\Mediawesome;

use DNADesign\Elemental\Models\ElementContent;
use nglasl\mediawesome\MediaPage;
use nglasl\mediawesome\MediaHolder;
use nglasl\mediawesome\MediaTag;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\ListboxField;
use SilverStripe\ORM\DataList;

/**
 * ElementMediawesome
 * Adds an element listing matching mediawesome child records
 */
class ElementMediawesome extends ElementContent {

    /**
     * @inheritdoc
     */
    private static $icon = 'font-icon-thumbnails';

    /**
     * @inheritdoc
     */
    private static $table_name = 'ElementMediawesome';

    /**
     * @inheritdoc
     */
    private static $title = 'Mediawesome list';

    /**
     * @inheritdoc
     */
    private static $description = "Display a list of Mediawesome items";

    /**
     * @inheritdoc
     */
    private static $singular_name = 'Mediawesome';

    /**
     * @inheritdoc
     */
    private static $plural_name = 'Mediawesomes';

    /**
     * @inheritdoc
     */
    private static $db = [
        'NumberOfPosts' => 'Int',
        'MediaHolderLinkTitle' => 'Varchar(255)'
    ];

    /**
     * @inheritdoc
     */
    private static $defaults = [
        'NumberOfPosts' => 4
    ];

    /**
     * @inheritdoc
     */
    private static $has_one = [
        'MediaHolder' => MediaHolder::class,
        'Tag' => MediaTag::class
    ];

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return _t(__CLASS__ . '.BlockType', 'Mediawesome list');
    }

    /**
     * @inheritdoc
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function($fields)
        {
                $fields->removeByName(['MediaHolderID','TagID']);

                $tags = MediaTag::get()->map('ID', 'Title');

                $fields->addFieldsToTab(
                    'Root.Main', [
                        DropdownField::create(
                            'MediaHolderID',
                            _t(
                                __CLASS__ . '.HOLDER_ID', 'Choose a media holder'
                            ),
                            $this->getMediaHolders()
                        )->setEmptyString('Choose an option'),
                        TextField::create(
                            'MediaHolderLinkTitle',
                            _t(
                                __CLASS__ . '.LINKTITLE', 'Media holder link title'
                            )
                        ),
                        DropdownField::create(
                            'TagID',
                            'Tag',
                            $tags
                        )->setEmptyString('Choose an option'),
                        NumericField::create(
                            'NumberOfPosts',
                            _t(
                                __CLASS__ . '.POSTS', 'Number of Posts'
                            )
                        )->setDescription(
                            _t(
                                __CLASS__ . '.POSTS_DESCRIPTION', 'Setting this value to zero will return all matching posts'
                            )
                        )
                    ]
                );

            });
        return parent::getCMSFields();
    }

    /**
     * @inheritdoc
     */
    public function onBeforeWrite() {
        parent::onBeforeWrite();
        $this->NumberOfPosts = abs($this->NumberOfPosts);
    }

    /**
     * Return all MediaHolder objects
     */
    public function getMediaHolders() : DataList {
        return MediaHolder::get();
    }

    /**
     * Get all recent posts based on filters and limit
     */
    public function getRecentPosts() : ?DataList
    {
        $mediaHolder = $this->MediaHolder();
        if(!$mediaHolder || !$mediaHolder->exists()) {
            return null;
        }

        $mediaPages = MediaPage::get()->sort('Date', 'DESC')->filter([
            'ParentID' => $mediaHolder->ID
        ]);

        $tag = $this->Tag();
        if($tag && $tag->exists() && $tag->Title) {
            $mediaPages = $mediaPages->filter([
                'Tags.Title' => $tag->Title
            ]);
        }

        if ($mediaPages && $this->NumberOfPosts > 0) {
            $mediaPages = $mediaPages->limit($this->NumberOfPosts);
        }

        return $mediaPages;
    }


}
