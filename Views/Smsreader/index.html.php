<?php
$view->extend('MauticCoreBundle:Default:content.html.php');
$view['slots']->set('headerTitle', $view['translator']->trans('smsreader.title'));
?>


<div class="container">
    <p>Enter your configuration for the SMS Reader plugin below</p>
    <?php echo $view['form']->start($form) ?>
    <?php echo $view['form']->widget($form) ?>
    <?php echo $view['form']->end($form) ?>
</div>