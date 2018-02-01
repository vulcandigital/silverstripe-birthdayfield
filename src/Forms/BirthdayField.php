<?php

namespace Vulcan\BirthdayField\Forms;

use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\FormField;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\ORM\DataObjectInterface;
use SilverStripe\ORM\FieldType\DBDate;

/**
 * Class BirthdayField
 * @package Vulcan\iHelp\Forms
 */
class BirthdayField extends FormField
{
    /** @var string The format that will be displayed when converted to readonly */
    private static $format = 'd/m/Y';

    /** @var FieldList|DropdownField[] */
    public $children;

    /** @var DropdownField */
    protected $dayField;

    /** @var DropdownField */
    protected $monthField;

    /** @var DropdownField */
    protected $yearField;

    /** @var bool */
    protected $bootstrapRender = false;

    /**
     * BirthdayField constructor.
     *
     * @param string $name
     * @param null   $title
     * @param null   $value
     */
    public function __construct($name, $title = null, $value = null)
    {
        $this->children = new FieldList([
            $this->dayField = DropdownField::create("{$name}[Day]", 'Day', $this->getDayMap())->setEmptyString('Day'),
            $this->monthField = DropdownField::create("{$name}[Month]", 'Month', $this->getMonthMap())->setEmptyString('Month'),
            $this->yearField = DropdownField::create("{$name}[Year]", 'Year', $this->getYearMap())->setEmptyString('Year'),
        ]);

        parent::__construct($name, $title, $value);

        if ($value) {
            $this->setValue($value);
        }
        $this->setTitle($title);
    }

    /**
     * @return array
     */
    public function getDayMap()
    {
        $map = [];

        for ($i = 1; $i <= 31; $i++) {
            $map[$i] = $i;
        }

        return $map;
    }

    /**
     * @return array
     */
    public function getMonthMap()
    {
        $map = [];

        for ($m = 1; $m <= 12; $m++) {
            $month = date('F', mktime(0, 0, 0, $m, 1));
            $map[$m] = $month;
        }

        return $map;
    }

    /**
     * @param int $offset
     *
     * @return array
     */
    public function getYearMap($offset = 100)
    {
        $now = (int)date('Y');
        $start = $now - $offset;
        $map = [];

        for ($i = 0; $i <= $offset; $i++) {
            $map[$start + $i] = $start + $i;
        }

        return $map;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setRightTitle($title)
    {
        foreach ($this->children as $field) {
            /** @var FormField $field */
            $field->setRightTitle($title);
        }

        return $this;
    }

    /**
     * @param mixed $value
     * @param null  $data
     *
     * @return $this
     */
    public function setValue($value, $data = null)
    {
        $date = new DBDate();
        $date->setValue($value);

        $this->getDayField()->setValue(date('j', $date->getTimestamp()));
        $this->getMonthField()->setValue(date('n', $date->getTimestamp()));
        $this->getYearField()->setValue(date('Y', $date->getTimestamp()));

        return $this;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->getDayField()->setName("{$name}[Day]");
        $this->getMonthField()->setName("{$name}[Month]");
        $this->getYearField()->setName("{$name}[Year]");

        parent::setName($name);

        return $this;
    }

    public function Field($properties = [])
    {
        return $this->renderWith($this->bootstrapRender ? BirthdayField::class . 'Bootstrap' : BirthdayField::class);
    }

    /**
     * Makes a read only field with some stars in it to replace the password
     *
     * @return ReadonlyField
     */
    public function performReadonlyTransformation()
    {
        /** @var ReadonlyField $field */
        $field = $this->castedCopy(ReadonlyField::class)->setTitle($this->title ? $this->title : 'Birthday');

        return $field;
    }

    /**
     * @return ReadonlyField
     */
    public function performDisabledTransformation()
    {
        return $this->performReadonlyTransformation();
    }

    /**
     * @param DataObjectInterface $record
     */
    public function saveInto(DataObjectInterface $record)
    {
        if ($this->name) {
            $birthday = sprintf('%s-%s-%s', $this->getYearField()->Value(), $this->getMonthField()->Value(), $this->getDayField()->Value());
            $birthday = strtotime($birthday);

            $record->setCastedField($this->name, $birthday);
        }
    }

    /**
     * @return DropdownField|null
     */
    public function getDayField()
    {
        return $this->dayField;
    }

    /**
     * @return DropdownField|null
     */
    public function getMonthField()
    {
        return $this->monthField;
    }

    /**
     * @return DropdownField|null
     */
    public function getYearField()
    {
        return $this->yearField;
    }

    /**
     * @return FieldList
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return false|string
     */
    public function Value()
    {
        return date($this->config()->get('format'), strtotime(sprintf('%s-%s-%s', $this->getYearField()->Value(), $this->getMonthField()->Value(), $this->getDayField()->Value()))) ?? null;
    }

    /**
     * @return mixed
     */
    public function getBootstrapRender()
    {
        return $this->bootstrapRender;
    }

    /**
     * @param bool $bootstrapRender
     *
     * @return $this
     */
    public function setBootstrapRender($bootstrapRender)
    {
        $this->bootstrapRender = $bootstrapRender;

        return $this;
    }

    /**
     * Disables the day, month, year labels on each of the dropdown fields
     *
     * @return $this
     */
    public function disableTitles()
    {
        foreach ($this->children as $child) {
            $child->setTitle(null);
        }

        return $this;
    }
}
