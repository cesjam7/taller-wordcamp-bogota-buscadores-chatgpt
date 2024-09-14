<h1><?php the_title(); ?></h1>
<form method="GET">
  <label>Qué deseas comprar, puedes agregar detalles</label><br>
  <input type="text" name="busqueda" style="width: 300px;"
    placeholder="Ej: Zapatillas para correr en oferta color negro..."><br>
  <input type="submit" value="Buscar">
</form>

<?php if (isset($_GET['busqueda'])) {
  
  if ($_GET['edad'] > 18) {
    // Code to execute if edad > 18
  } else {
    // Code to execute if edad <= 18
  }

  $respuesta = chatgpt_taller([
    [
      'role' => 'system', 'content' => 'De lo que te voy a pasar entre comillas extrae el producto, categoria, marca, color, oferta',
    ],
    [
      'role' => 'system', 'content' => 'El nombre del producto debe ser lo más simple posible, sin agregar detalles'
    ],
    [
      'role' => 'system', 'content' => 'Solo responde con un JSON con este formato: {"producto": $nombre_producto, "categoria": runner/futbol/tracking, "marca": "$marca1, $marca2, $marca3", "oferta": 1/0, "color": $color }'
    ],
    [
      'role' => 'user', 'content' => '"'.$_GET['busqueda'].'"'
    ]
  ]); ?>
  <h2>Resultado de busqueda</h2>
  <p>Información extraida: <?php echo $respuesta; ?></p>

  <?php $data = json_decode($respuesta);
  $args = [
    's' => $data->producto,
    'post_type' => 'product',
    'posts_per_page' => 10,
    'tax_query' => [],
    'meta_query' => []
  ];
  if ($data->categoria != '') {
    $termIds = get_terms([
      'taxonomy' => 'product_cat',
      'name__like' => $data->categoria,
      'fields' => 'ids'
    ]);
    // print_r($termIds);
    if ($termIds) {
      array_push($args['tax_query'], [
        [
          'taxonomy' => 'product_cat',
          'field' => 'id',
          'terms' => $termIds,
        ]
      ]);
    }
  }
  if ($data->marca != '') {
    $marcas = explode(',', $data->marca);
    $marcas = array_map('trim', $marcas);
    $termIds = [];
    foreach ($marcas as $marca) {
      $ids = get_terms([
        'taxonomy' => 'brand',
        'name__like' => $marca,
        'fields' => 'ids'
      ]);
      if ($ids) $termIds = array_merge($termIds, $ids);
    }
    // print_r($termIds);
    if ($termIds) {
      array_push($args['tax_query'], [
        [
          'taxonomy' => 'brand',
          'field' => 'id',
          'terms' => $termIds,
          'operator' => 'IN'
        ]
      ]);
    }
  }
  if (isset($data->oferta)) {
    array_push($args['meta_query'], [
      'key' => '_sale_price',
      'value' => 0,
      'compare' => '>'
    ]);
  }
  echo '<pre>'.print_r($args, 1).'</pre>';
  $query = new WP_Query($args);
  if ($query->have_posts()) {
    while ($query->have_posts()) { $query->the_post(); ?>
      <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
    <?php }
  } else { ?>
    <p>No se encontraron resultados</p>
  <?php }
}