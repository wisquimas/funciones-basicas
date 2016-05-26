<?php
if( !function_exists('etiquetas_og') ){
	function etiquetas_og(){
		global $post;
		$title		= get_bloginfo( 'name' );
		$type		= 'website';
		$url		= get_home_url();
		$image		= '';
		$description= get_bloginfo( 'description' );
		
		if( is_single() && get_post_type() == 'post' ){
			$title		= get_the_title( $post->ID );
			$type		= 'article';
			$url		= get_permalink( $post->ID );
			//$image_indi	= get('imagenes_principales_imagen_principal_interna',1,1,$post->ID);
			$image		= $image_indi ? $image_indi : $image;
			$description= strip_tags( substr( $post->post_content, 0,147) ).'...';
		};
		/*COLOCAR META ETIQUETAS DE WORDPRESS*/
		?>
        <meta property="og:title" content="<?php echo $title;?>" />
		<meta property="og:type" content="<?php echo $type;?>" />
		<meta property="og:url" content="<?php echo $url;?>" />
		<meta property="og:image" content="<?php echo $image;?>" />
		<meta property="og:description" content="<?php echo $description;?>" />
        <?php
	};
};
?>