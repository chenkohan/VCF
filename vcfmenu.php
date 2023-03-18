<?php
session_start();

$settings = array(
	//規則: 0-普規 1-日規 2-gomoku(未實作 也沒有對應的題庫)
	'RULE' => 0,
	//題庫難度 依手數排
	'lv' => 3,
	//遊戲模式: 0-練習模式 1-盲解模式 2-比賽模式 3-綜合模式
	'MODE' => 0,
	//遊戲者名稱預設Player
	'NIKE' => 'Player'
);
//判斷本php頁面被呼叫的時候有沒有接收到$settings對應的傳入值
//當沒有接收到任何變數輸入時,填入預設值
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$settings['RULE'] = isset($_POST['RULE']) ? $_POST['RULE'] : $settings['RULE'];
	$settings['lv'] = isset($_POST['lv']) ? $_POST['lv'] : $settings['lv'];
	$settings['MODE'] = isset($_POST['MODE']) ? $_POST['MODE'] : $settings['MODE'];
	$settings['NIKE'] = isset($_POST['NIKE']) ? $_POST['NIKE'] : $settings['NIKE'];
}
?>
<!DOCTYPE HTML>
<html>

<HEAD>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=450px, initial-scale=0.9">
	<!--	<link rel=stylesheet href="puzzle.css">-->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>

</HEAD>

<body>


	<h1 color='#ff8307'>衝四勝VCF詰棋挑戰</h1>

	<form id="levelf">
		<h2 color='#ff8307'>規則：</h2>
		<select id="rule" size=1>
			<option value=0 selected>普規(無禁手長連也算勝)</option>
			<option value=1>日規(黑有禁手,白可逼禁勝)</option>
		</select>

		<h2 color='#ff8307'>難度：</h2>
		<select id="level" size=1></select>

		<h2 color='#ff8307'>遊戲模式：</h2>
		<select id="gameMode" size=1>
			<option value=0 selected>練習模式(基礎)</option>
			<option value=1>盲解模式(挑戰)</option>
			<option value=2>比賽模式(高手)</option>
			<option value=3>綜合模式(鍛鍊)</option>
		</select>
		<h3 id="modeExp" color='#ff8307'></h3>

		<!--<input id="rank" type="button" value="Rank" onClick="location.href='prank.php'">-->

		<div>Nickname ：<br>
			<input id="nikeName" type="text" name="comment" rows="1" cols="50" value="<?php echo $nike; ?>"></input>
		</div>
	</form>
	<br><br>
	<table>
		<tr>
			<td><input id="set" type="button" value="Start game"></td>
			<td>&nbsp;&nbsp;&nbsp;</td> <!-- 使用實體空格插入空白 -->
			<td><input id="reset" type="button" value="選項初始化"></td>
		</tr>
	</table>


</body>

</html>

<script type="text/javascript">
	const VC4MIN = 3;
	const VC4MAX = 14;

	const settings = <?php echo json_encode($settings); ?>;

	$("#level").html(() => {
		let options = "";
		for (let i = VC4MIN; i <= VC4MAX; i++) {
			options += `<option value="${i}">${i}</option>`;
		}
		return options;
	}).val(settings.lv);

	$("#rule").val(settings.RULE);
	$("#gameMode").val(settings.MODE).on("change", updateModeExplanation);
	$("#nikeName").val(settings.NIKE);


	$("#set").click(() => {
		const S = Math.max($("#level").val() - 2, 3);
		const B = $("#level").val();
		const M = $("#gameMode").val();
		const N = $("#nikeName").val();
		const R = $("#rule").val();

		const url = `renju_board.php?type=VC4&S=${S}&B=${B}&RULE=${R}&MODE=${M}&NIKE=${N}`;

		// 使用 window.top.location.href 替換 location.href，以確保在完整瀏覽器視窗中打開頁面
		window.top.location.href = url;
	});

	updateModeExplanation();

	//記錄各選項的設定 讓下次載入時套用
	$("#level").on("change", function () {
		localStorage.setItem("level", $(this).val());
	});

	$("#gameMode").on("change", function () {
		localStorage.setItem("gameMode", $(this).val());
		updateModeExplanation();
	});

	$("#nikeName").on("input", function () {
		localStorage.setItem("nikeName", $(this).val());
	});

	$("#rule").on("change", function () {
		localStorage.setItem("rule", $(this).val());
	});
	$("#reset").click(() => {
		localStorage.removeItem("rule");
		localStorage.removeItem("level");
		localStorage.removeItem("gameMode");
		localStorage.removeItem("nikeName");
		location.reload();
	});


	function updateModeExplanation() {
		const mode = $("#gameMode").val();
		let explanation = "";

		switch (parseInt(mode)) {
			case 0:
				explanation = "練習模式:每一手衝四都會直接在棋盤上落子,而且防守方會自動落子擋四(或先連五),當你成功連五會跳下一題<br><br>";
				break;
			case 1:
				explanation = "盲解模式:每一手都會幫你標記數字手順並保留在棋盤上(不會有錯誤提示),<br>當你點到活四或雙四時,按下核對答案按鈕會幫你檢查,失敗重來,成功會跳下一題<br>(在按下核對按鈕前,不管按幾次重來跟退回上一步都算同一次作答)";
				break;
			case 2:
				explanation = "比賽模式:棋盤上只會標示目前你所點的最後一子手順(不會有錯誤提示),<br>當你點到活四或雙四時,按下核對答案按鈕會幫你檢查,失敗重來,成功會跳下一題<br>(在按下核對按鈕前,不管按幾次重來跟退回上一步都算同一次作答)";
				break;
			case 3:
				explanation = "綜合模式:每題第一次答題會採用比賽模式,答錯一次會改為盲解模式,<br>答錯兩次以上會變成練習模式,直到解對時才跳下一題,<br>這是最推薦的鍛鍊方式,您可以在過程中自行驗證思考的盲點";
				break;
		}


		$("#modeExp").html(explanation);
	}
	$(document).ready(function () {
		const storedLevel = localStorage.getItem("level");
		const storedGameMode = localStorage.getItem("gameMode");
		const storedNikeName = localStorage.getItem("nikeName");
		const storedRule = localStorage.getItem("rule");

		if (storedLevel !== null) {
			$("#level").val(storedLevel);
		}
		if (storedGameMode !== null) {
			$("#gameMode").val(storedGameMode);
			updateModeExplanation();
		}
		if (storedNikeName !== null) {
			$("#nikeName").val(storedNikeName);
		}
		if (storedRule !== null) {
			$("#rule").val(storedRule);
		}
	});
</script>