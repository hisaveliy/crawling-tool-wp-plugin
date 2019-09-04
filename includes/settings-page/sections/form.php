<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2019-06-14
 * Time: 23:39
 */
?>

<form id='options_form' method='post' name='form'>

    <?php echo $fields; ?>

    <br>

    <input
        name="save"
        type="button"
        value="<?php _e('Save', 'savellab');?> "
        class="button button-primary"
        onclick="submit_form(this, document.forms['form'])" />

    <input type="hidden" name="formaction" value="default" />

    <script> function submit_form(element, form){
            form['formaction'].value = element.name;
            form.submit();
        } </script>

</form>
