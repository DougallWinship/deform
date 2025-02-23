<div style="display:flex; flex-direction: row">
    <div style="width:25%; display:flex; flex-direction:column">
        <?php foreach(\Deform\Component\ComponentFactory::getRegisteredComponents() as $component){ ?>
            <div><?= $component ?></div>
        <?php } ?>
    </div>
    <div style="flex-grow:1">
    </div>
    <div style="width:25%">
    </div>
</div>