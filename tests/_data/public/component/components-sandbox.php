<?php
$defaultNamespace = "builder";
?>
<script>
    <?= \Deform\Component\ComponentFactory::getCustomElementDefinitionsJavascript(false); ?>
</script>
<div id="form-builder">
    <div id="available-components">
        <h3>Available Components</h3>
        <?php foreach(\Deform\Component\ComponentFactory::getRegisteredComponents() as $component){ ?>
            <div
                class="component component-<?= $component ?>"
                draggable="true"
                data-component="<?= $component ?>">
                <?= $component ?>
            </div>
        <?php } ?>
    </div>

    <form id="form-area" data-namespace="<?= $defaultNamespace ?>" method="post">
        <h3>Form Area</h3>
        <p>Drag components here</p>
        <button type="button" id="dump-button">Log Form Data</button>
        <div id="form-data"></div>
    </form>

    <div id="form-info">
        <h3>Form Info</h3>
        <div id="form-definition">
            <label>Form namespace<input id="form-namespace-input" type="text" value="<?= $defaultNamespace ?>" autocomplete="off" /></label>
            <p>Details about the form & any selected component will appear here.</p>
        </div>
        <div id="dynamic-component-info">
        </div>
    </div>
</div>
<link rel='stylesheet' href='/assets/sandbox.css?version=<?= uniqid() ?>'>
<script lang='text/javascript' src='/assets/sandbox.js?version=<?= uniqid() ?>'></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        window.deformSandboxApp = new DeformSandbox.SandboxApp(
            <?= json_encode(\Deform\Component\ComponentFactory::getRegisteredComponents()) ?>,
            document.querySelector("#form-builder"),
            document.querySelector("#form-builder #available-components"),
            document.querySelector("#form-builder #form-area"),
            document.querySelector("#form-builder #form-info"),
        );
    });
</script>

