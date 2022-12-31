<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	session_start();

	if( !isset($_SESSION['login']) ) {
		header('Location: /cms/login/');
	}

	include_once '../../config/Database.php';
	include_once '../../models/models.php';
	include_once '../../login/cms_admins.php';
	include_once '../../lib/funcs.php';

	$database = new Database();
	$conn = $database->connect();
	$isAdmin = in_array( $_SESSION['name'], $admins );
	
	$ip4 = getUserIpAddr();
	$query = "SELECT clinic,username FROM users LEFT JOIN clinics ON users.clinic_fk=clinics.ID WHERE users.ip4='$ip4'";
	$stmt = $conn->prepare( $query );
	$stmt->execute();
	$result = $stmt->fetch( PDO::FETCH_ASSOC );
	
	class ServiceFormControl {
		public $ID;
		public $Category;
		public $Service;
		public $Available;

		public function __construct( $arr ) {
			$this->ID = $arr['ID'];
			$this->Category = $arr['category'];
			$this->Service = $arr['service'];
			$this->Available = $arr['available'];
		}

		public function createFormControl() {
			$str = '<div class="form-check form-switch">';
			$str .= '<input class="form-check-input" type="checkbox" id="' . $this->ID . '"';
			$str .= ($this->Available == 1) ? ' checked>' : '>';
			$str .= '<label class="form-check-label" for="' . $this->ID . '">' . $this->Service . '</label>';
			$str .= '</div>';
			return $str;
		}
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" 
	      rel="stylesheet" 
			integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" 
			crossorigin="anonymous">
	<link rel="stylesheet" href="/cms/assets/css/style.css">
	<style>
		<?php if( !$isAdmin ) { ?>
			form.row { display: none; }
		<?php } ?>
	</style>
</head>
<body id="menu-page">
	<?php include '../menus/navigation.html'; ?>

	<div class="clinic-menu container pb-0" id="filter">
		<div class="m-3">
			<h1>Clinic Menu</h1>
			<p>Modify service offerings</p>

			<form method="post" id="clinic-select" class="row row-cols-lg-auto g-3 align-items-end">
				<div class="d-none">
					<label for="territory">Territory:</label>
					<select name="territory" id="territory" class="form-select">
						<option value="">All</option>
					</select>
				</div>
				
				<div class="d-none">
					<label for="state">State:</label>
					<select name="state" id="state" class="form-select">
						<option value="">All</option>
					</select>
				</div>

				<div class="d-none">
					<label for="region">Region:</label>
					<select name="region" id="region" class="form-select">
						<option value="">All</option>
					</select>
				</div>

				<select 
					title="clinic listing"
					name="clinic" 
					id="clinic" 
					class="form-select" 
					data-selected-clinic="<?php echo ( isset($_POST['clinic']) ) ? $_POST['clinic'] : '' ?>"
					style="width: auto;"
				>
					<option value="">-Select Clinic-</option>
				</select>
				<a href="#" id="clear-btn" class="btn btn-secondary mx-3 d-none">Clear</a>
			</form>
		</div>
	</div>

	<div class="clinic-services">
		<div class="row">
			<div class="col">
				<h3>
					<?php
						$q = getCurrentAndNextQuarter();
						echo $q[0]; 
					?>
				</h3>
				<?php
					$query_results = null;

					if( $_SERVER['REQUEST_METHOD'] == 'GET' && $result && !$isAdmin ) {
						$query_results = getClinicServices( $result['clinic'], $q[0] );
					}

					if( isset( $_POST['clinic'] ) ) {
						$query_results = getClinicServices( $_POST['clinic'], $q[0] );
					}

					if( $query_results ) {
						foreach ( $query_results as $category => $service ) {
							echo '<div class="category m-3"><header>' . $category . '</header>';
							
							foreach ($service as $input) {
								echo $input->createFormControl();
							}
							
							echo '</div>';
						}
					}
				?>
			</div>

			<div class="col">
				<h3><?php echo $q[1]; ?></h3>
				<?php
					$query_results = null;

					if( $_SERVER['REQUEST_METHOD'] == 'GET' && $result && !$isAdmin ) {
						$query_results = getClinicServices( $result['clinic'], $q[1] );
					}

					if( isset( $_POST['clinic'] ) ) {
						$query_results = getClinicServices( $_POST['clinic'], $q[1] );
					}

					if( $query_results ) {
						foreach ( $query_results as $category => $service ) {
							echo '<div class="category m-3"><header>' . $category . '</header>';
							
							foreach ($service as $input) {
								echo $input->createFormControl();
							}
							
							echo '</div>';
						}
					}
				?>
			</div>
		</div>
	</div>

	<div class="bottom-bar" style="display: block<?php // echo $query_results ? 'block' : 'none'; ?>;">
		<button type="button" class="btn btn-lg btn-primary">Update</button>
	</div>

	<div class="status-message">
		<div class="message">Status</div>
	</div>

	<script src="/cms/lib/utils.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" 
	        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" 
			  crossorigin="anonymous">
	</script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.10.3/gsap.min.js"></script>
	
	<script>
		var data = {};

		async function postData(url, payload) {
			const response = await fetch(url, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify(payload)
			});
			return response.json();
		}

		document.addEventListener('DOMContentLoaded', function() {
			document.querySelector('#clinic').addEventListener('change', function(evt) {
				document.getElementById('clinic-select').submit();
			});

			Array.from(document.querySelectorAll('input[type=checkbox]')).forEach(input => {
				input.addEventListener('change', function() {
					data[this.id] = this.checked;
					console.log(this.id, this.checked);
				});
			});

			document.querySelector('button').addEventListener('click', function() {
				postData('/cms/api/update/update.php', data).then(function(status) {
					var message = document.querySelector('.message');

					if(status.msg == 'success') {
						message.textContent = 'Save Successful!';
						message.style.background = 'green';
						data = {};
					} 
					else if(status.msg == 'failed') {
						message.textContent = 'Save Failed!';
						message.style.background = 'red';
					}
					else {
						message.textContent = 'Nothing changed';
						message.style.background = 'gray';
					}

					document.querySelector('.status-message').classList.add('show');
					setTimeout(function() {
						document.querySelector('.status-message').classList.remove('show');
					}, 2000);
				});
			});

			let options = {
				root: null,
				rootMargin: '0px',
				threshold: 0.5
			}

			let callback = function(entries, observer) {
				entries.forEach(function(entry) {
					if(entry.isIntersecting) {
						gsap.to(entry.target, {x: 0, opacity: 1});
					}
				});
			};

			let observer = new IntersectionObserver(callback, options);
			let categories = Array.from(document.querySelectorAll('.category'));

			if(categories.length > 0) {
				categories.forEach(category => {
					observer.observe(category);
				});
			}
		});
	</script>
</body>
</html>