<h1>Buscador conversación</h1>
<?php if (empty($_GET)) { ?>
  <form method="GET">
    <label>Hola! Qué deseeas comprar</label><br>
    <input type="text" name="busqueda" placeholder="Deseo..."><br>
    <input type="submit" value="Buscar">
  </form>
<?php }
if (isset($_GET['busqueda'])) {

  $respuesta = chatgpt_taller([
    [
      'role' => 'system',
      'content' => 'De la búsqueda que te voy a pasar entre comillas extrae el producto que desea comprar el cliente'
    ],
    [
      'role' => 'system',
      'content' => 'Solo responde con un JSON {producto: $nombre_simple}'
    ],
    [
      'role' => 'user',
      'content' => '"'.$_GET['busqueda'].'"'
    ]
  ]);
  $respuesta = json_decode($respuesta);
  if (isset($respuesta->producto)) { ?>
    <form method="GET">
      <input type="hidden" name="producto" value="<?php echo $respuesta->producto; ?>">
      <label>Y de qué marca deseas el <?php echo $respuesta->producto; ?></label><br>
      <input type="text" name="marca" placeholder="Marca ..."><br>
      <input type="submit" value="Buscar">
    </form>

  <?php } else {
    echo '<pre>'.print_r($respuesta, true).'</pre>';
  }

} ?>

<?php if (isset($_GET['marca'])) {
  $respuesta = chatgpt_taller([
    [
      'role' => 'system',
      'content' => 'Del texto que te voy a pasar entre comillas extrae el nombre de la marca',
    ],
    [
      'role' => 'system',
      'content' => 'Solo responde como un JSON el nombre de la marca {marca: Adidas/Nike/Puma/Reebok}'
    ],
    [
      'role' => 'user',
      'content' => '"'.$_GET['marca'].'"'
    ]
  ]);
  $respuesta = json_decode($respuesta);
  // echo 'Respuesta: ';print_r($respuesta);

  $marcas = get_terms([
    'taxonomy' => 'brand',
    'hide_empty' => false,
    'name__like' => $respuesta->marca,
    'fields' => 'ids'
  ]);

  $args = [
    'post_type' => 'product',
    's' => $_GET['producto'],
    'tax_query' => [
      [
        'taxonomy' => 'brand',
        'field' => 'id',
        'operator' => 'IN',
        'terms' => $marcas
      ]
    ]
  ];
  echo '<pre>';print_r($args);echo '</pre>';
  $productos = new WP_Query($args);
  while ($productos->have_posts()) { $productos->the_post();

    the_title();
    echo '<hr>';

  } wp_reset_postdata();
} ?>