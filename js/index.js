let countInterval;
let btnStatus = false;
let focusStatus = true;
let subNameAry = [];
let progress = 0;
let deleteTaskId = [];
let runTask;
let unloggedTaskId = 0;
let unloggedContent = [];
let unloggedsubNameAry = [];
let userId = $("header").attr("data-id");

if (userId) {
  axios({
    method: "GET",
    url: "/TomatoClock/api/api_get_task.php",
  }).then((response) => {
    if (response.data.success) {
      let data = response.data.data;
      data.forEach((item) => {
        appendTask(item.id, item.name, item.progress, item.amount);
      });
    }
  });

  axios({
    method: "GET",
    url: "/TomatoClock/api/api_get_setting.php",
  }).then((response) => {
    if (response.data.success) {
      let data = response.data.data;
      $("#tomatoTime").val(data.tomato_time);
      $("#shortTime").val(data.short_break_time);
      $("#longTime").val(data.long_break_time);
      $("#cycle").val(data.long_break_cycle);
      $("#alarmSound").val(data.ring);
      $("#number").text($("#tomatoTime").val() + ":00");
    }
  });

  $("#poll").on("click", function () {
    $("#chart").empty();
    axios({
      method: "GET",
      url: "/TomatoClock/api/api_get_statistical_data.php",
    }).then(function (response) {
      if (response.data.success) {
        let data = response.data.data;
        new Chart($("#chart"), {
          type: "bar",
          data: {
            labels: [
              "星期一",
              "星期二",
              "星期三",
              "星期四",
              "星期五",
              "星期六",
              "星期日",
            ],
            datasets: [
              {
                label: "番茄數", // 標籤
                data: [
                  data[1],
                  data[2],
                  data[3],
                  data[4],
                  data[5],
                  data[6],
                  data[0],
                ], // 資料
                backgroundColor: [
                  // 背景色
                  "rgba(255, 99, 132, 0.2)",
                  "rgba(255, 99, 132, 0.2)",
                  "rgba(255, 99, 132, 0.2)",
                  "rgba(255, 99, 132, 0.2)",
                  "rgba(255, 99, 132, 0.2)",
                  "rgba(255, 99, 132, 0.2)",
                  "rgba(255, 99, 132, 0.2)",
                ],
                borderColor: [
                  "rgba(255,99,132,1)",
                  "rgba(255,99,132,1)",
                  "rgba(255,99,132,1)",
                  "rgba(255,99,132,1)",
                  "rgba(255,99,132,1)",
                  "rgba(255,99,132,1)",
                  "rgba(255,99,132,1)",
                ],
                borderWidth: 1, // 外框寬度
              },
            ],
          },
        });
      } else {
        alert("內容有誤，請重新嘗試");
      }
    });
  });
}

$("#startBtn").on("click", function () {
  btnStatus = !btnStatus;
  if (btnStatus) {
    loading(true);
    $(this).text("停止");
    start();
  } else {
    $(this).text("開始");
    loading(false);
    stop();
  }
});

$("#taskList").on("click", function (e) {
  let id = e.target.id;
  if (id.split("_")[0] === "changeStyle") {
    let taskId = id.split("_")[1];
    if (runTask !== taskId) {
      $(`#task_${runTask}`).removeAttr("style", "");
      $(`#task_${taskId}`).css("background-color", "#FFD2D2");
      progress = Number($(`#amount_${taskId}`).text().split("/")[0]);
      runTask = taskId;
    }
  }

  let taskId = id.split("_")[2];
  if (id.split("_")[0] === "more") {
    $("#taskCard").remove();
    if (userId) {
      axios({
        method: "POST",
        url: "/TomatoClock/api/api_get_task.php",
        data: {
          task_id: taskId,
        },
      }).then((response) => {
        if (response.data.success) {
          let data = response.data.data;
          appendTaskCard("taskList");
          $(`#task_${data.id}`).hide();
          $("#addTask").css("display", "none");
          $("#taskName").val(data.name);
          $("#content").val(data.content);
          $("#amount").val(data.amount);
          if (data.subtask.length >= 2) {
            let x = 0;
            data.subtask.forEach((item) => {
              x += 1;
              $("#subtaskList").append(
                `<li id="subtaskName${x}">${item.name}</li>`
              );
            });
          } else if (data.subtask.length === 1) {
            $("#subtaskList").append(
              `<li id="subtaskName${data.subtask.length}">${data.subtask[0].name}</li>`
            );
          }
          if (data.amount > 4) {
            $("#subtask").css("display", "inline");
          }

          dealWithTask(subNameAry);

          $("#cancel").on("click", function () {
            $("#addTask").css("display", "inline");
            $("#taskCard").remove();
            $(`#task_${data.id}`).show();
          });

          $("#saveBtn").on("click", function () {
            let name = $("#taskName").val();
            let content = $("#content").val();
            let amount = $("#amount").val();
            axios({
              method: "post",
              url: "/TomatoClock/api/api_update_task.php",
              data: {
                task_id: data.id,
                name: name,
                content: content,
                amount: amount,
                subtaskName: subNameAry,
              },
            }).then((response) => {
              $(`#name_${data.id}`).text(name);
              $(`#amount_${data.id}`).text(data.progress + "/" + amount);
              $(`#task_${data.id}`).show();
              $("#taskCard").remove();
              $("#addTask").css("display", "inline");
            });
          });
        } else {
          alert("內容有誤，請正確輸入");
        }
      });
    } else {
      let taskName = $(`#name_${taskId}`).text();
      let amount = $(`#amount_${taskId}`).text().split("/")[1];
      let progress = $(`#amount_${taskId}`).text().split("/")[0];
      appendTaskCard("taskList");
      $(`#task_${taskId}`).hide();
      $("#addTask").css("display", "none");
      $("#taskName").val(taskName);
      $("#content").val(unloggedContent[taskId]);
      $("#amount").val(amount);
      if (unloggedsubNameAry[taskId].length >= 2) {
        let x = 0;
        unloggedsubNameAry[taskId].forEach((item) => {
          x += 1;
          $("#subtaskList").append(`<li id="subtaskName${x}">${item}</li>`);
        });
      } else if (unloggedsubNameAry[taskId].length === 1) {
        $("#subtaskList").append(
          `<li id="subtaskName1">${unloggedsubNameAry[taskId][0]}</li>`
        );
      }
      if (amount > 4) {
        $("#subtask").css("display", "inline");
      }
      dealWithTask(unloggedsubNameAry[taskId]);

      $("#cancel").on("click", function () {
        $("#addTask").css("display", "inline");
        $("#taskCard").remove();
        $(`#task_${taskId}`).show();
      });

      $("#saveBtn").on("click", function () {
        let name = $("#taskName").val();
        let content = $("#content").val();
        let amount = $("#amount").val();
        $(`#name_${taskId}`).text(name);
        $(`#amount_${taskId}`).text(progress + "/" + amount);
        unloggedContent[taskId] = content;
        $(`#task_${taskId}`).show();
        $("#taskCard").remove();
        $("#addTask").css("display", "inline");
      });
    }
  }

  switch (id.split("_")[0]) {
    case "uncheck":
      $(`#uncheck_btn_${taskId}`).hide();
      $(`#check_btn_${taskId}`).show();
      $(`#name_${taskId}`).css("text-decoration", "line-through");
      deleteTaskId.push(taskId);
      break;
    case "check":
      $(`#uncheck_btn_${taskId}`).show();
      $(`#check_btn_${taskId}`).hide();
      $(`#name_${taskId}`).css("text-decoration", "none");
      let x = 0;
      deleteTaskId.forEach(function (item) {
        x += 1;
        if (item === taskId) {
          deleteTaskId.splice(x - 1, 1);
        }
      });
      break;
  }
});

$("#addTask").on("click", function () {
  $(this).css("display", "none");
  appendTaskCard("body", "disabled");
  dealWithTask(subNameAry);

  $("#cancel").on("click", function () {
    $("#addTask").css("display", "inline");
    $("#taskCard").remove();
  });

  $("#saveBtn").on("click", function () {
    let name = $("#taskName").val();
    let content = $("#content").val();
    let amount = $("#amount").val();
    if (userId) {
      axios({
        method: "post",
        url: "/TomatoClock/api/api_add_task.php",
        data: {
          name: name,
          content: content,
          amount: amount,
          subtaskName: subNameAry,
        },
      }).then((response) => {
        if (response.data.success) {
          let data = response.data.data;
          appendTask(data.task_id, name, 0, amount);
        } else {
          alert("內容錯誤，請重新輸入");
        }
      });
    } else {
      unloggedTaskId += 1;
      unloggedContent[unloggedTaskId] = content;
      unloggedsubNameAry[unloggedTaskId] = subNameAry;
      appendTask(unloggedTaskId, name, 0, amount);
    }
    $("#taskForm").trigger("reset");
    $("#subtask").css("display", "none");
    $("#subtaskList").empty();
    $("#saveBtn").attr("disabled", true);
  });
});

$("#deleteAllTask").on("click", function () {
  if (userId) {
    axios({
      method: "delete",
      url: "/TomatoClock/api/api_delete_task.php",
    }).then((response) => {
      if (!response.data.success) {
        alert("刪除失敗");
      }
    });
  }
  $("#taskList").empty();
});

$("#deleteTasks").on("click", function () {
  if (userId) {
    axios({
      method: "delete",
      url: "/TomatoClock/api/api_delete_task.php",
      data: {
        task_id: deleteTaskId
      },
    }).then((response) => {
      if (!response.data.success) {
        alert("刪除失敗");
      }
    });
  }
  deleteTaskId.forEach((taskId) => {
    $(`#task_${taskId}`).remove();
  });
});

var myModalEl = document.getElementById("settingModal");
myModalEl.addEventListener("hide.bs.modal", function (event) {
  if (
    !$("#settingBtn").on("click", function () {
      return true;
    })
  ) {
    $("#settingForm").trigger("reset");
  }
});

$("#settingBtn").on("click", function () {
  if (userId) {
    axios({
      method: "POST",
      url: "/TomatoClock/api/api_update_setting.php",
      data: {
        tomatoTime: $("#tomatoTime").val(),
        shortTime: $("#shortTime").val(),
        longTime: $("#longTime").val(),
        cycle: $("#cycle").val(),
        alarmSound: $("#alarmSound").val(),
      },
    }).then((response) => {
      if (!response.data.success) {
        alert("內容有誤");
      }
    });
  }
  $("#number").text($("#tomatoTime").val() + ":00");
});

$("#alarmSound").on("change", function () {
  let name = $(this).val();
  sound(name);
});

function appendTaskCard(id, disabled = "") {
  $(`#${id}`).append(`
      <div id="taskCard" class="card shadow-sm rounded mt-3" style="max-width: 36rem;width:100%;">
        <form id="taskForm">
          <div class="card-body">
            <input id="taskName" type="text" class="form-control" placeholder="今天有甚麼任務呢?" autocomplete=off>
            <div class="row mt-4">
              <div class="col col-lg-8">
                <h6>備註</h6>
                <textarea class="form-control" id="content" cols="20" rows="4"></textarea>
              </div>
              <div class="col col-lg-4">
                <h6>番茄數</h6>
                <div class="d-flex">
                  <input id="amount" type="text" class="form-control form-control me-3" value="1">
                  <button id="dropUp" type="button" class="btn btn-light btn-sm me-1">
                    <span class="material-icons" style="font-size:20px; color:black">arrow_drop_up</span>
                  </button>
                  <button id="dropDown" type="button" class="btn btn-light btn-sm">
                    <span class="material-icons" style="font-size:20px; color:black">arrow_drop_down</span>
                  </button>
                </div>
              </div>
            </div>
            <div id="subtask" style="display: none">
              <h6 class="mt-4">子任務</h6>
              <div class="d-flex border-bottom w-50">
                <span class="material-icons mt-1 btn" id="addSubtaskBtn">add</span>
                <input type="text" class="form-control border-0" id="subtaskName" placeholder="添加子任務" autocomplete=off>
              </div>
              <ul class="ms-2 mt-2" id="subtaskList">
              </ul>
            </div>
            <div class="d-flex justify-content-end mt-3">
              <button id="cancel" type="button" class="btn btn-secondary me-2">
                取消
              </button>
              <button id="saveBtn" type="button" class="btn btn-primary" ${disabled}>
                儲存
              </button>
            </div>
          </div>
        </form>
      </div>
      `);
}

function appendTask(id, name, progress = 0, amount) {
  $("#taskList").append(
    `
      <div id="task_${id}" class="card mt-2" style="cursor: pointer;">
        <div id="changeStyle_${id}" class="d-flex justify-content-between p-2 shadow-sm rounded">
          <div class="d-flex">
            <span id="uncheck_btn_${id}" class="material-icons btn">
              radio_button_unchecked
            </span>
            <span id="check_btn_${id}" class="material-icons btn" style="display: none">
              check_circle_outline
            </span>
            <h6 id="name_${id}" class="mt-2">${name}</h6>
          </div>
          <div class="d-flex">
            <h6 id="amount_${id}" class="mt-2">${progress}/${amount}</h6>
            <span id="more_vert_${id}" class="more_vert material-icons btn" style="color:black;font-size:25px">more_vert</span>
          </div>
        </div>
      </div>
    `
  );
}

function dealWithTask(subNameAry) {
  $("#taskName").on("change", function () {
    if ($(this).val()) {
      $("#saveBtn").removeAttr("disabled");
    } else {
      $("#saveBtn").attr("disabled", true);
    }
  });

  $("#dropUp").on("click", function () {
    $("#amount").val((amount = parseInt($("#amount").val())) + 1);
    if (amount >= 4) {
      $("#subtask").css("display", "inline");
    } else {
      $("#subtask").css("display", "none");
    }
  });

  $("#dropDown").on("click", function () {
    if ((amount = parseInt($("#amount").val())) > 1) {
      $("#amount").val((amount -= 1));
      if (amount <= 4) {
        $("#subtask").css("display", "none");
      }
    }
  });

  $("#addSubtaskBtn").on("click", function () {
    if ((subtaskName = $("#subtaskName").val())) {
      subNameAry[subNameAry.length] = subtaskName;
      $("#subtaskList").append(
        `<li id="subtaskName${subNameAry.length}">${subtaskName}</li>`
      );
      $("#subtaskName").val("");
    }
  });
}

function paddedFormat(num) {
  return num < 10 ? "0" + num : num;
}

function focusCount() {
  time = $("#number").text().split(":");
  minute = time[0];
  second = minute * 60 + parseInt(time[1]);
  second -= 1;
  if (second <= 0) {
    sound($("#alarmSound").val());
    progress += 1;
    if (userId) {
      axios({
        method: "POST",
        url: "/TomatoClock/api/api_update_task.php",
        data: {
          task_id: runTask,
          progress: progress,
        },
      }).then(function (response) {
        if (response.data.success) {
          if (!runTask) {
            progress = 0;
          } else {
            $(`#amount_${runTask}`).text(
              progress + "/" + response.data.data.amount
            );
          }
        } else {
          alert("更新錯誤");
        }
      });
    } else {
      if (!runTask) {
        progress = 0;
      } else {
        let amount = $(`#amount_${runTask}`).text().split("/")[1];
        $(`#amount_${runTask}`).text(progress + "/" + amount);
      }
    }
    clearInterval(countInterval);
    takeBreak();
    return;
  }
  min = parseInt(second / 60);
  sec = parseInt(second % 60);
  $("#number").text(`${paddedFormat(min)}:${paddedFormat(sec)}`);
}

function breakCount() {
  time = $("#number").text().split(":");
  minute = time[0];
  second = minute * 60 + parseInt(time[1]);
  second -= 1;
  if (second <= 0) {
    sound($("#alarmSound").val());
    clearInterval(countInterval);
    focus();
    return;
  }
  min = parseInt(second / 60);
  sec = parseInt(second % 60);
  $("#number").text(`${paddedFormat(min)}:${paddedFormat(sec)}`);
}

function start() {
  if (focusStatus) {
    countInterval = setInterval(focusCount, 1000);
  } else {
    countInterval = setInterval(breakCount, 1000);
  }
}

function stop() {
  clearInterval(countInterval);
}

function takeBreak() {
  shortTime = $("#shortTime").val();
  longTime = $("#longTime").val();
  focusStatus = false;
  $("#title").text("休息時間");
  if (!(progress % $("#cycle").val() === 0)) {
    $("#number").text(`${paddedFormat(shortTime)}:${paddedFormat(0)}`);
  } else {
    $("#number").text(`${paddedFormat(longTime)}:${paddedFormat(0)}`);
  }
  $("#startBtn").removeClass("btn-danger").addClass("btn-primary");
  countInterval = setInterval(breakCount, 1000);
}

function focus() {
  focusTime = $("#tomatoTime").val();
  focusStatus = true;
  $("#title").text("專注時間");
  $("#number").text(`${paddedFormat(focusTime)}:${paddedFormat(0)}`);
  $("#startBtn").removeClass("btn-primary").addClass("btn-danger");
  countInterval = setInterval(focusCount, 1000);
}

function loading(boolen) {
  if (boolen) {
    let x = 1;
    if (focusStatus) {
      $("#title").text("專注時間");
    } else {
      $("#title").text("休息時間");
    }
    loadInterval = setInterval(function () {
      if (x > 3) {
        x = 0;
        $("#first").hide();
        $("#second").hide();
        $("#third").hide();
      }

      switch (x) {
        case 1:
          $("#first").show();
          break;
        case 2:
          $("#second").show();
          break;
        case 3:
          $("#third").show();
          break;
      }
      x += 1;
    }, 1000);
  } else {
    $("#first").hide();
    $("#second").hide();
    $("#third").hide();
    $("#title").text("繼續專注吧!");
    clearInterval(loadInterval);
  }
}

function sound(name) {
  const music = new Audio(`sound/${name}.mp3`);
  let playPromise = music.play();

  if (playPromise !== undefined) {
    playPromise
      .then(() => {
        music.loop = true;
        time = setTimeout(() => {
          music.pause();
        }, 5000);
      })
      .catch((error) => {
        console.log(error);
      });
  }
}
