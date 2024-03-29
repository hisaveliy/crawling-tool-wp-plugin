<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2019-06-14
 * Time: 22:51
 */

if (isset($value['id'])) :

	if (!isset($value['std']))
		$value['std'] = '';

	$value = array_merge([
		'id' => $value['id'],
		'type' => 'text',
		'label' => '',
		'placeholder' => '',
		'value' => get_option($value['id']) === false ? $value['std'] : get_option($value['id']),
        'attrs' => ''
	], $value);
	?>

  <label for="<?php echo $value['id']; ?>">
	  <?php echo $value['label']; ?>

    <br>

    <input
      type="<?php echo $value['type']; ?>"
      id="<?php echo $value['id']; ?>"
      name="<?php echo $value['id']; ?>"
      placeholder="<?php echo $value['placeholder']; ?>"
      value="<?php echo $value['value']; ?>"
      <?php echo $value['attrs']?>/>
  </label>

<?php
endif;
