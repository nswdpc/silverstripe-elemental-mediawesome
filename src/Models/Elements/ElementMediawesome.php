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

/**
 * ElementMediawesome adds a featured video
 */
class ElementMediawesome extends ElementContent {

    private static $icon = 'font-icon-thumbnails';

    private static $table_name = 'ElementMediawesome';

    private static $title = 'Mediawesome list';
    private static $description = "Display a list of Mediawesome items";

    private static $singular_name = 'Mediawesome';
    private static $plural_name = 'Mediawesomes';

    public function getType()
    {
        return _t(__CLASS__ . '.BlockType', 'Mediawesome list');
    }

    private static $db = [
        'NumberOfPosts' => 'Int',
        'MediaHolderLinkTitle' => 'Varchar(255)'
    ];

    private static $defaults = [
        'NumberOfPosts' => 4
    ];

    private static $has_one = [
        'MediaHolder' => MediaHolder::class,
        'Tag' => MediaTag::class
    ];

    private static $card_columns = [
        '2' => 'Two',
        '3' => 'Three',
        '4' => 'Four',
    ];

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
                                __CLASS__ . 'HOLDER_ID', 'Choose a media holder'
                            ),
                            $this->getMediaHolders()
                        )->setEmptyString('Choose an option'),
                        TextField::create(
                            'MediaHolderLinkTitle',
                            _t(
                                __CLASS__ . 'LINKTITLE', 'Media holder link title'
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
                                __CLASS__ . 'POSTS', 'Number of Posts'
                            )
                        ),
                        DropdownField::create(
                            'CardColumns',
                            'Card columns',
                            $this->owner->config()->card_columns
                        )->setEmptyString('Choose an option')
                    ]
                );

            });
        return parent::getCMSFields();
    }

    public function getMediaHolders() {
        return MediaHolder::get();
    }

    public function getRecentPosts()
    {
        $mediaHolder = $this->MediaHolder();
        $mediaPages = MediaPage::get()->sort('Date', 'DESC')->filter([
            'ParentID' => $mediaHolder->ID,
            'Tags.Title' => $this->Tag()->Title
        ]);

        if ($mediaPages)
        {
          return $mediaPages->limit($this->NumberOfPosts);
        }

        return null;
    }

}
