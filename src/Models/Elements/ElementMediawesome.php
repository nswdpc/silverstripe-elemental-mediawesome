<?php

namespace NSWDPC\Elemental\Models\Mediawesome;

use DNADesign\Elemental\Models\ElementContent;
use nglasl\mediawesome\MediaPage;
use nglasl\mediawesome\MediaHolder;
use nglasl\mediawesome\MediaTag;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataList;

/**
 * ElementMediawesome
 * Adds an element listing matching mediawesome child records
 * @property int $NumberOfPosts
 * @property ?string $MediaHolderLinkTitle
 * @property int $MediaHolderID
 * @property int $TagID
 * @method \nglasl\mediawesome\MediaHolder MediaHolder()
 * @method \nglasl\mediawesome\MediaTag Tag()
 * @mixin \NSWDPC\GridHelper\Extensions\ElementChildGridExtension
 */
class ElementMediawesome extends ElementContent
{
    /**
     * @inheritdoc
     */
    private static string $icon = 'font-icon-thumbnails';

    /**
     * @inheritdoc
     */
    private static string $table_name = 'ElementMediawesome';

    /**
     * @inheritdoc
     */
    private static string $title = 'Mediawesome list';

    /**
     * @inheritdoc
     */
    private static string $description = "Display a list of Mediawesome items";

    /**
     * @inheritdoc
     */
    private static string $singular_name = 'Mediawesome';

    /**
     * @inheritdoc
     */
    private static string $plural_name = 'Mediawesomes';

    /**
     * @inheritdoc
     */
    private static array $db = [
        'NumberOfPosts' => 'Int',
        'MediaHolderLinkTitle' => 'Varchar(255)'
    ];

    /**
     * @inheritdoc
     */
    private static array $defaults = [
        'NumberOfPosts' => 4
    ];

    /**
     * @inheritdoc
     */
    private static array $has_one = [
        'MediaHolder' => MediaHolder::class,
        'Tag' => MediaTag::class
    ];

    /**
     * @inheritdoc
     */
    #[\Override]
    public function getType()
    {
        return _t(self::class . '.BlockType', 'Mediawesome list');
    }

    /**
     * @inheritdoc
     */
    #[\Override]
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function ($fields): void {
            $fields->removeByName(['MediaHolderID','TagID']);

            $tags = MediaTag::get()->map('ID', 'Title');

            $fields->addFieldsToTab(
                'Root.Main',
                [
                    DropdownField::create(
                        'MediaHolderID',
                        _t(
                            self::class . '.HOLDER_ID',
                            'Choose a media holder'
                        ),
                        $this->getMediaHolders()
                    )->setEmptyString(_t(self::class . '.CHOOSE_OPTION', 'Choose an option')),
                    TextField::create(
                        'MediaHolderLinkTitle',
                        _t(
                            self::class . '.LINKTITLE',
                            'Media holder link title'
                        )
                    ),
                    DropdownField::create(
                        'TagID',
                        _t(
                            self::class . '.TAG',
                            'Tag'
                        ),
                        $tags
                    )->setEmptyString(_t(self::class . '.CHOOSE_OPTION', 'Choose an option')),
                    NumericField::create(
                        'NumberOfPosts',
                        _t(
                            self::class . '.POSTS',
                            'Number of Posts'
                        )
                    )->setDescription(
                        _t(
                            self::class . '.POSTS_DESCRIPTION',
                            'Setting this value to zero will return all matching posts'
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
    #[\Override]
    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        $this->NumberOfPosts = abs($this->NumberOfPosts);
    }

    /**
     * Return all MediaHolder objects
     */
    public function getMediaHolders(): DataList
    {
        return MediaHolder::get();
    }

    /**
     * Get all recent posts based on filters and limit
     */
    public function getRecentPosts(): ?DataList
    {
        $mediaHolder = $this->MediaHolder();
        if (!$mediaHolder || !$mediaHolder->exists()) {
            return null;
        }

        $mediaPages = MediaPage::get()->sort(['Date' => 'DESC'])->filter([
            'ParentID' => $mediaHolder->ID
        ]);

        $tag = $this->Tag();
        if ($tag && $tag->exists() && $tag->Title) {
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
