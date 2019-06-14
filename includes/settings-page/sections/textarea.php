<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2019-06-14
 * Time: 22:51
 */

$value = array_merge([
    'id' => 'null',
    'placeholder' => '',
    'cols' => '',
    'rows' => '',
    'value' => get_option($value['id']) === false ? $value['std'] : get_option($value['id']),
], $value);
?>


<label for="<?php echo $value['id']; ?>">
    <?php echo $value['label']; ?>

    <br>

    <textarea
        id="<?php echo $value['id']; ?>"
        name="<?php echo $value['id']; ?>"
        cols="<?php echo $value['cols']; ?>"
        rows="<?php echo $value['rows']; ?>"><?php echo $value['value']; ?></textarea>

</label>