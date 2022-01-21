<?php require_once "layout/head.php" ?>

<header class="container" data-id="<?= $_SESSION['user_id'] ?? '' ?>">
	<div class="d-flex justify-content-between pt-3 mb-5 border-bottom">
		<a href="" class="d-flex text-dark text-decoration-none mt-2">
			<img rel="shortcut icon" sizes="16x16" href="img/outline_alarm_black_24dp.png">
			<!-- <span class="material-icons" style="font-size:40px">alarm</span> -->
			<h1>Pomodoro</h1>
		</a>
		<div class="btn">
			<span class="material-icons me-2" data-bs-toggle="modal" data-bs-target="#tipsModal">emoji_objects</span>
			<span id="poll" class="material-icons me-2" data-bs-toggle="modal" data-bs-target="#reportModal">poll</span>
			<span class="material-icons me-2" data-bs-toggle="modal" data-bs-target="#settingModal">settings</span>
			<?php if (!isset($_SESSION['logged_in'])) : ?>
				<a id="login" href="login.php">
					<span class="material-icons">account_circle</span>
				</a>
			<?php else : ?>
				<a id="logout" href="logout.php">
					<span class="material-icons">logout</span>
				</a>
			<?php endif ?>
		</div>
	</div>
</header>

<div id="container" class="container">
	<h2 class="d-flex py-2 justify-content-center fs-2">
		<div class="text-center" id="title">
			開始番茄鐘吧!
		</div>
		<div class="d-flex">
			<div id="first">.</div>
			<div id="second">.</div>
			<div id="third">.</div>
		</div>
	</h2>
	<div id="body" class="d-flex align-items-center flex-column">
		<div class="card shadow-sm rounded" style="max-width: 36rem;width:100%;">
			<div class="card-body row justify-content-center">
				<h1 class="text-center" id="number">25:00</h1>
				<button type="button" class="btn btn-danger" style="max-width: 20rem;width:100%;" id="startBtn">開始</button>
			</div>
		</div>
		<div class="d-flex align-items-center mt-5" style="width:100%; max-width: 36rem; border-bottom: 2px solid;">
			<h5 class="me-auto">任務列表</h5>
			<div class="dropdown">
				<button class="btn" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
					<span id="tasks_more_vert" class="material-icons" style="color:black;font-size:25px">more_vert</span>
				</button>
				<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
					<li><a id="deleteAllTask" class="dropdown-item" href="#">清除所有任務</a></li>
					<li><a id="deleteTasks" class="dropdown-item" href="#">清除計時完任務</a></li>
				</ul>
			</div>
		</div>

		<div class="mt-2" id="taskList" style="max-width: 36rem;width:100%;">
		</div>

		<button id="addTask" class="btn btn-secondary mt-3" style="max-width: 36rem;width:100%;">
			<div class="d-flex justify-content-center align-items-center">
				<span style="font-size:25px;color:#fbfbfb" class="material-icons">add_circle</span>
				<h5 class="mt-2 ms-1">新增任務</h5>
			</div>
		</button>

	</div>
</div>

<div class="modal fade" id="settingModal" tabindex="-1" aria-labelledby="settingModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="settingModalLabel">設定</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form id="settingForm">
				<ul class="modal-body list-group list-group-flush">
					<li class="d-flex list-group-item p-3">
						<div>
							<label class="form-label">番茄時間</label>
							<input class="form-control" id="tomatoTime" type="number" value="25" min="1">
						</div>
						<div>
							<label class="form-label">短時休息</label>
							<input class="form-control" id="shortTime" type="number" value="5" min="1">
						</div>
						<div>
							<label class="form-label">長時休息</label>
							<input class="form-control" id="longTime" type="number" value="15" min="1">
						</div>
					</li>
					<li class="list-group-item d-flex align-items-center justify-content-between p-3">
						<h6>長時休息循環</h6>
						<input id="cycle" class="form-control" style="max-width:70px;width:100%" type="number" min="1" step="1" value="4">
					</li>
					<li class="list-group-item d-flex align-items-center justify-content-between p-3 mt-3">
						<h6>鈴聲種類</h6>
						<div>
							<select name="alarmSound" id="alarmSound" class="form-select ms-auto" aria-label="Default select example" style="max-width:100px;width:100%">
								<option id="bell" value="bell">鐘聲</option>
								<option id="summerBell" value="summerBell">盛夏鐘</option>
								<option id="doorBell" value="doorBell">門鈴</option>
								<option id="bird" value="bird">鳥鳴</option>
							</select>
						</div>
					</li>
				</ul>
			</form>
			<div class="modal-footer">
				<!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
				<button type="button" id="settingBtn" class="btn btn-primary" data-bs-dismiss="modal">儲存</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="settingModalLabel">本週番茄數統計圖表</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<?php if (!isset($_SESSION['logged_in'])) : ?>
					<div class="row justify-content-center mt-4">
						<h2 id="reminder" class="col-12 text-center">請登入，並享有完整功能</h2>
						<a href="/TomatoClock/login.php" class="btn btn-primary col-4 mt-4">登入頁面</a>
					</div>
				<?php endif; ?>
				<canvas id="chart"></canvas>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="tipsModal" tabindex="-1" aria-labelledby="tipsModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h2 class="modal-title" id="tipsModallLabel">如何正確使用番茄鐘?</h2>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<ul class="list-group list-group-flush">
					<li class="list-group-item">
						<h4>1.別急著開始，先"擬定計畫"</h4>
						每項任務的目的與帶來的收穫，並且思考任務對你來說的重要與緊急程度去安排先後順序
					</li>
					<li class="list-group-item">
						<h4>2.每個任務的番茄別大於4個</h4>
						當一個任務所需番茄鐘大於4個，那代表這個任務太耗時間，需要分割成子任務，讓自己更清楚任務架構
					</li>
					<li class="list-group-item">
						<h4>3.番茄鐘不可分割、暫停</h4>
						只要開始了番茄鐘，那你唯一可以做的只有番茄鐘交代的事，如果途中被打斷了，那這個番茄鐘就是作廢、重設!
					</li>
					<li class="list-group-item">
						<h4>4.超過或提早結束番茄鐘</h4>
						如果這項任務最後的番茄已經響了，但你卻還沒完成任務，沒關係繼續做，但是一旦超過太長時間就得歸在下個番茄裡；反之，提早做完的話，那就一樣等這個番茄停，在這期間回想剛剛完成這項任務的收穫是否滿足
					</li>
					<li class="list-group-item">
						<h4>5."休息"非常的重要</h4>
						每個番茄結束，就要好好休息3-5分鐘，而如果完成4個番茄的循環就得長時休息15-30分。休息可以讓頭腦消化、吸收剛才25分的工作內容，不要想剛剛工作內容，放鬆頭腦或想工作以外的事情都可以
					</li>
				</ul>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<script src="js/index.js"></script>
<?php require_once "layout/footer.php" ?>