<?php
/**
 * This is responsible for processing AJAX or other requests
 *
 * @since      1.0.0
 */

namespace Savellab_Plugin;

class Requests {

  /**
   * This init AJAX listeners
   *
   * @since 1.0.0
   */
  public static function ajax(){}

  /**
   * Proceeds a requests using eCurring api key
   *
   * @param array $array
   * @return mixed
   */
  public static function get($array = array()) {
    if (!$array || empty($array))
      return null;

    $url = isset($array['url']) ? $array['url'] : '';
    $query = '';
    $headers = array();
    $setopt_array = array(
      CURLOPT_RETURNTRANSFER => 1,
    );

    if (!$url)
      return null;

    if (isset($array['query']) && !empty($array['query']))
      $query = '?' . http_build_query($array['query']);

    if (isset($array['headers'])) :
      foreach ($array['headers'] as $key => $value) :
        $headers[] = "{$key}: $value";
      endforeach;
    endif;

    $setopt_array[CURLOPT_HTTPHEADER] = $headers;

    $setopt_array[CURLOPT_URL] = $url . $query;

    $curl = curl_init();

    curl_setopt_array($curl, $setopt_array);

    $resp = curl_exec($curl);

    curl_close($curl);

    return json_decode($resp, 1);
  }

  /**
   * Proceeds a requests using eCurring api key
   *
   * @param array $array
   * @return mixed
   */
  public static function post($array = array()) {
    if (!$array || empty($array))
      return null;

    $url = isset($array['url']) ? $array['url'] : '';
    $query = '';
    $headers = array();
    $setopt_array = array(
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_POST => 1,
    );

    if (!$url)
      return null;

    if (isset($array['query']) && !empty($array['query']))
      $setopt_array[CURLOPT_POSTFIELDS] = $array['query'];

    if (isset($array['headers'])) :
      foreach ($array['headers'] as $key => $value) :
        $headers[] = "{$key}: $value";
      endforeach;
    endif;

    $setopt_array[CURLOPT_HTTPHEADER] = $headers;

    $setopt_array[CURLOPT_URL] = $url . $query;


    $curl = curl_init();

    curl_setopt_array($curl, $setopt_array);

    $resp = curl_exec($curl);

    curl_close($curl);

    return json_decode($resp, 1);
  }

  /**
   * Proceeds a requests using eCurring api key
   *
   * @param array $array
   * @return mixed
   */
  public static function patch($array = array()) {
    if (!$array || empty($array))
      return null;

    $url = isset($array['url']) ? $array['url'] : '';
    $query = '';
    $headers = array();
    $setopt_array = array(
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_CUSTOMREQUEST => 1,
    );

    if (!$url)
      return null;

    if (isset($array['query']) && !empty($array['query']))
      $setopt_array[CURLOPT_POSTFIELDS] = $array['query'];

    if (isset($array['headers'])) :
      foreach ($array['headers'] as $key => $value) :
        $headers[] = "{$key}: $value";
      endforeach;
    endif;

    $setopt_array[CURLOPT_HTTPHEADER] = $headers;

    $setopt_array[CURLOPT_URL] = $url . $query;


    $curl = curl_init();

    curl_setopt_array($curl, $setopt_array);

    $resp = curl_exec($curl);

    curl_close($curl);

    return json_decode($resp, 1);
  }
}