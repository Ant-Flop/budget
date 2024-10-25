document.addEventListener("keydown", (event) => {
  if (event.key === "Enter") loginOnClick();
});

// requests

const loginRequest = (login, password, nameApp) => {
  const xhr = new XMLHttpRequest();
  const requestURL = "login.php";
  xhr.open("POST", requestURL);
  xhr.send(
    JSON.stringify({
      login: login,
      password: password,
      nameApp: nameApp,
    })
  );
  xhr.onload = function () {
    console.log(xhr.response);
    console.log(JSON.parse(xhr.response));
    let response = JSON.parse(xhr.response);
    if (response.status) routeUser(response.session_info.route);
    else callErrorFiled(response.text_error);
  };
};

const routeUser = (route) => {
  location.replace(route);
};

// events

const loginOnClick = () => {
  const login = document.getElementById("login-input").value;
  const password = document.getElementById("password-input").value;
  const nameAppSelect = document.getElementById("name-app");
  const nameApp = nameAppSelect.options[nameAppSelect.selectedIndex].value;
  if (validationForm(login, "Логін") && validationForm(password, "Пароль"))
    loginRequest(login, password, nameApp);
  else return;
};

const validationForm = (value, typeInput) => {
  return value === "" ? callErrorFiled("Заповніть поле - " + typeInput) : true;
};

const callErrorFiled = (textError) => {
  const warningField = document.getElementById("warning-field");
  warningField.style.visibility = "visible";
  warningField.innerText = textError;
  return false;
};
