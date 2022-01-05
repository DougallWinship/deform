<?php
namespace Deform\Component;

use Deform\Html\Html as Html;
use Deform\Html\IHtml;


class Select extends BaseComponent
{
    /** @var IHtml */
    public $select;

    public $hasOptGroups=false;

    public function setup()
    {
        $this->select = Html::select([
            'id' => $this->getId(),
            'name' => $this->getName(),
            'autocomplete' => 'off'
        ]);
        $this->control($this->select);
    }

    public function options($options): Select
    {
        $this->select->clear();
        $isAssoc = \Deform\Util\Arrays::isAssoc($options);
        foreach ($options as $key=>$value) {
            $this->select->add(
                Html::option(['value'=> $isAssoc ? $key : $value])->add($value)
            );
        }
        return $this;
    }

    public function optgroupOptions(array $optgroupOptions): Select
    {
        $this->select->clear();
        $this->hasOptGroups = true;
        foreach ($optgroupOptions as $groupName=>$options) {
            $optgroup = Html::optgroup(['label'=>$groupName]);
            $isAssoc = \Deform\Util\Arrays::isAssoc($options);
            foreach ($options as $key=>$value) {
                $optgroup->add(
                    Html::option(['value'=> $isAssoc ? $key : $value])->add($value)
                );
            }
            $this->select->add($optgroup);
        }
        return $this;
    }

    /**
     * @param string $value
     * @throws \Exception
     */
    public function setSelected($value): Select
    {
        if (is_array($value)) {
            throw new \Exception("Only one option can be selected for a non-multi select");
        }
        $this->setSelectedForValues([$value]);
        return $this;
    }

    /**
     * @param array $valueArray
     */
    protected function setSelectedForValues(array $valueArray)
    {
        foreach ($this->select->getChildren() as $child) {
            if ($child->getTagType()=='optgroup') {
                foreach($child->getChildren() as $optgroupChild) {
                    if (in_array($optgroupChild->get('value'),$valueArray)) {
                        $optgroupChild->set('selected','selected');
                    }
                    else {
                        $child->unset('selected');
                    }
                }
            }
            else {
                $check = $child->get('value');
                if (in_array($check,$valueArray)) {
                    $child->set('selected','selected');
                }
                else {
                    $child->unset('selected');
                }
            }
        }
    }

    public function beforeRender()
    {
        if ($this->select->isEmpty()) {
            throw new \Exception("You haven't set any select options!");
        }
    }
}
