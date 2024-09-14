<?php
add_action('init', 'wordcamp_init');
function wordcamp_init() {
  register_taxonomy('brand', 'product', [
    'label' => 'Marca',
    'hierarchical' => true,
  ]);
}

function chatgpt_taller($prompts) {
  $url = 'https://api.openai.com/v1/chat/completions';
  $response = wp_remote_post($url, [
    'headers' => [
      'Content-Type' => 'application/json',
      'Authorization' => 'Bearer '.APIKEY_CHATGPT
    ],
    'body' => json_encode([
      'model' => 'gpt-3.5-turbo',
      'messages' => $prompts
    ])
  ]);
  if (!is_wp_error($response)) {
    // echo '<pre>'.print_r($response['body'], true).'</pre>';
    $data = json_decode($response['body']);
    if (isset($data->choices[0]->message->content)) {
      return $data->choices[0]->message->content;
    } else {
      echo '<pre>'.print_r($data, 1).'</pre>';die;
    }
  } else {
    echo 'Error: '.$response->get_error_message();die;
  }
}