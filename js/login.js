$("form").on("submit", function (e) {
  e.preventDefault();
  axios({
    method: "POST",
    url: "/TomatoClock/api/api_login.php",
    data: {
      email: $("#email").val(),
      password: $("#password").val(),
    },
  })
    .then((response) => {
      if (response.data.success) {
        window.location.href = "/TomatoClock";
      } else {
        console.log(response.data.message);
        $("#err_msg").text(response.data.message);
      }
    })
    .catch((err) => {
      console.log(err);
    });
});