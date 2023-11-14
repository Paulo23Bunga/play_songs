<?php 
function conexao(){
	$dsn = "mysql:host=localhost;dbname=mediaplayer";
	$user = "root";
	$password= "";
	$conn = new PDO($dsn, $user, $password);
	return $conn;
}

function buscarNome($num=6){
	$caracteres = 'abcdefghijklmnopqrstuvwxyz0123456789';
	$string = '';
	$max = strlen($caracteres) -1;
	for ($i = 0; $i < $num; $i++){
		$string = $caracteres[mt_rand(0, $max)];
	}
	return $string;
}

function sava_media($filename, $descricao){
	$conn = conexao();
	$sql = "INSERT INTO media(file, descricao) VALUES (?,?)";
	$query = $conn->prepare($sql);
	$query->execute([$filename, $descricao]);
}

function get_media(){
	$result = [];
	try{
		$conn = conexao();
		$result = $conn->query("SELECT * FROM media");
	}	catch(Exception $e){
		echo $e->getMessage();
	}
	return $result;
}

if($_SERVER['REQUEST_METHOD'] = 'POST' && isset($_POST['save'])){
	$uploadDir ="./upload/";
if(isset($_FILES['file']) && $_FILES['file']['error'] == 0){
	
	$filename = $_FILES['file']['name'];
	$filetype = $_FILES['file']['type'];
	$filesize = $_FILES['file']['size'];
	$newFileName = buscarNome().".".pathinfo($filename, PATHINFO_EXTENSION);
	if(file_exists($uploadDir . $newFileName)){
		echo $filename . 'O arquivo existe';
	}else{
		move_uploaded_file($_FILES['file']['tmp_name'], $uploadDir . $newFileName);
		sava_media($newFileName, $filename);
		echo "<h3>Cadastro com successo</h3>";
	}
}	
}
?>

<!DOCTYPE html>
<html lang="pt-pt">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width-device-width, initial-scale=1">
	<title>Media PLayer</title>
	
	<link href="css/bootstrap.min.css" rel="stylesheet">
	
</head>
<body>
	<div class="container">
	<h3>MP3 PLAYR</h3>
	<form method="post" enctype="multipart/form-data">
		<input type="file" name="file" />
		<button type="submit" name="save">Salvar</button>
	</form>
	<br>
	Actions: <button id="pause">Pause</button>
	<button id="iniciar">Iniciar</button>
	
	<ul>
	<?php foreach(get_media() as $media): ?>
		<li><a class="play" href="javascript:void(0);" data-file="./upload/<?php echo $media['file']; ?>" id="play">
		<?php echo $media['descricao']; ?>
		</a></li>
	<?php endforeach; ?>	
		
	</ul>	
		
	</div>
	
<script src="js/jquery-1.9.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
<script>
var audio = null;
var currentFile = null;

	$(document).ready(function(){
		$('.play').on('click', function(){
			//alert("Ola Mundo");
			var el = $(this);
			var filename = el.attr('data-file');
		if(audio && currentFile === filename){
			audio.currentTime = 0;
			audio.play();
		}else{
			if(audio){
				audio.pause();
			}
			audio = new Audio(filename);
			currentFile = filename;
			audio.play();
		}
		
			return false;
		});
		
		$('#pause').on('click', function(){	
	
			
			if(audio){
				audio.pause();
			}
			return false;  
			
		});
		
		$('#iniciar').click(function(){
			if(audio){
				currentTime = 0;
				audio.play();
			}
			return false;
		});
		
	})

</script>

</html>