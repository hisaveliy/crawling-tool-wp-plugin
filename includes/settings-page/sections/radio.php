<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2019-06-14
 * Time: 22:51
 */

$value = array_merge([
    'id' => 'null',
    'type' => 'radio',
    'std' => get_option($value['id']),
], $value);
?>

<label class="radio">
    <input
        type="radio"
        name="<?php echo $value['id']; ?>"
        value="<?php echo $current_value; ?>"
        <?php checked($value['std'], $current_value); ?> /><?php echo $label; ?></label>
