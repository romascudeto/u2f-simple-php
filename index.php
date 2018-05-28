<html>
<head>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/custom.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.10/lodash.min.js"></script>
    <script src="js/u2f-api.js"></script>
    <script src="js/util.js"></script>

    <title>U2F SIMPLE PHP</title>
    <script>
        var arrayUser = [
            {"keyHandle":"FJAgUO1CNngNWUpA4zKWr946mdESN27XSdjkNeJwntNb07NbYkblRnvb2Q67zDK3xXk6bjNWoEiarhnwteJtzg","publicKey":"BAFTAhevngCmbmtN5NJoYCJdWK\/zH2BQ0uKHIA\/l3ENaMzt\/UYv3ydtULjZKQstIk7gX7a5ASkVhnDWjmo81gNk=","certificate":"MIICTzCCATegAwIBAgIEB4Y3zDANBgkqhkiG9w0BAQsFADAuMSwwKgYDVQQDEyNZdWJpY28gVTJGIFJvb3QgQ0EgU2VyaWFsIDQ1NzIwMDYzMTAgFw0xNDA4MDEwMDAwMDBaGA8yMDUwMDkwNDAwMDAwMFowMTEvMC0GA1UEAwwmWXViaWNvIFUyRiBFRSBTZXJpYWwgMjM5MjU3MzM5MjM4OTUyNDQwWTATBgcqhkjOPQIBBggqhkjOPQMBBwNCAASmGejk+OTcVUXeLx1ssrotfebUM7kVRhVPMt9tXQ0I\/eDGgXaAv47UA51Wx3qvTxiihVeLC5FobYNF+9ne\/T9vozswOTAiBgkrBgEEAYLECgIEFTEuMy42LjEuNC4xLjQxNDgyLjEuMTATBgsrBgEEAYLlHAIBAQQEAwIFIDANBgkqhkiG9w0BAQsFAAOCAQEAi+\/xexDdIiPTrzI8faFFZU94thbe0t1PDl4t1yoCbKYHcl099Gys9JM47mAtaUfWtEubUTBD2QPVQjBHSM1brtEppkZEOul4ulYxg1oaZXbyMxHBlF2mA+i7055Mc4Bxx7ghe0gUt\/tFYbYqqmQot9TvAPvQCke93yPS5fF8ysFBd8mGOxhaHbYZJz+hLnqHDcf8XfiZUulQx6iNCfPNCvGsls3eqLZ3RIMggZFsmh9tdUB3+uFzgAJChQcDTcp9NGueLOtd+k9+0e4+qv4OKnxJmb7DxTB51eGV51In5iLinbIBNhPBcv8zknjf80S3FVEapJaI6K4qbOTB6AJuvg==","email":"romascudeto@yahoo.com","password":"12345"}
        ];
        $(document).ready(function(){

                    for(var i=0;i<arrayUser.length;i++){
                        $(".table").find("tbody")
                        .append($("<tr>")
                            .append($("<td style='word-wrap:break-word;font-size:12px'>")
                            .text(arrayUser[i].email)
                            )
                            .append($("<td style='word-wrap:break-word;font-size:12px'>")
                            .text(arrayUser[i].password)
                            )
                            .append($("<td style='word-wrap:break-word;font-size:12px'>")
                            .text(arrayUser[i].keyHandle)
                            )
                        );
                    }
        });
        
        _u = _.noConflict(); 
        function showLogin(){
            $(".register-form").hide();
            $(".login-form").show();
        }
        function showRegister(){
            $(".register-form").show();
            $(".login-form").hide();
        }
        function submitRegister(){
            $.getJSON("registration.php", function(challenge) {
                $('.model-register-u2f').modal('show');
                console.log("Received challenge:\n" + JSON.stringify(challenge, null, "\t"));
                console.log("Calling the token for registration. Please press the button on the U2F token");
                
                u2f.register(challenge.appId, [challenge], [], function(data) {
                        // console.log(data);
                    if (!isU2fError(data)) {
                        registerValidation(data, challenge);
                    }
                }, 30);
            });
        }
        function registerValidation(deviceResponse, challenge) {
            var credentialObj = {
                "email" : $("#emailRegistration").val(),
                "password" : $("#passwordRegistration").val()
            }
            console.log(deviceResponse, challenge, credentialObj);
            $.ajax({
                type: "POST",
                url: "registrationValidation.php",
                data: { 
                    deviceResponse: JSON.stringify(deviceResponse),
                    challenge: JSON.stringify(challenge),
                    credential: JSON.stringify(credentialObj)
                },
                success: function(deviceReg){
                    deviceReg = JSON.parse(deviceReg);
                    arrayUser.push(deviceReg);
                    $(".table").find("tbody").html("");
                    for(var i=0;i<arrayUser.length;i++){
                        $(".table").find("tbody")
                        .append($("<tr>")
                            .append($("<td style='word-wrap:break-word;font-size:12px'>")
                            .text(arrayUser[i].email)
                            )
                            .append($("<td style='word-wrap:break-word;font-size:12px'>")
                            .text(arrayUser[i].password)
                            )
                            .append($("<td style='word-wrap:break-word;font-size:12px'>")
                            .text(arrayUser[i].keyHandle)
                            )
                        );
                    }
                    $('.model-register-u2f').modal('hide');
                }
            });
        }
        function submitLogin(){
            $(".successResult").html("");
            var result = _u.filter(arrayUser, { 'email': $("#emailLogin").val(), 'password':$("#passwordLogin").val() });
            $.ajax({
                type: "POST",
                url: "authentication.php",
                data: { 
                    registrations: JSON.stringify(result)
                },
                success: function(res){
                    res = JSON.parse(res);
                    var startAuthen = res;
                    $('.model-login-u2f').modal('show');
                    u2f.sign(startAuthen.appId, startAuthen.challenge,
                    [ { version: startAuthen.version, keyHandle: startAuthen.keyHandle } ],
                    function(data) {
                        loginValidation(result, startAuthen, data);
                    }, 30);
                }
            });
        }
        function loginValidation(result, startAuthen, data) {
            $.ajax({
                type: "POST",
                url: "authenticationValidation.php",
                data: { 
                    registrations: JSON.stringify(result),
                    authenticationRequest: JSON.stringify(startAuthen),
                    authenticationResponse: JSON.stringify(data)
                },
                success: function(res){                    
                    var response = JSON.parse(res);
                    console.log(response);
                    if (response.errorCode != "0"){
                        alert("Authentication Failed ! Error Code : "+response.errorCode);
                    }else{
                        $(".successResult").html(JSON.stringify(response.data));
                    }
                }
            });
        }
    </script>
</head>
<body>
<div class="container">
    <div class="u2f-header">U2F SIMPLE PHP</div><br>
    <table class="table" style="width:100%">
        <thead>
            <tr>
            <th>Email</th>
            <th>Password</th>
            <th>Keyhandle</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    <div class="login-form">
    <h2>Login</h2>
        <form>
        <div class="form-group">
            <label for="exampleInputEmail1">Email address</label>
            <input type="email" class="form-control" id="emailLogin" placeholder="Enter email" value="romascudeto@yahoo.com">
        </div>
        <div class="form-group">
            <label for="exampleInputPassword1">Password</label>
            <input type="password" class="form-control" id="passwordLogin" placeholder="Password" value="12345">
        </div>
        <button type="button" class="btn btn-primary" onClick="submitLogin()">Submit</button>
        <button type="button" class="btn btn-default" onClick="showRegister()">Register</button>

        <div class="modal fade model-login-u2f" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        U2F Login
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <h5>Please enter your FIDO U2F device into your computer's USB port. Then confirm registration on the device.</h5>
                        <div class="successResult" style="word-wrap: break-word"></div>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>
    <div class="register-form">
    <h2>Register</h2>
        <form>
        <div class="form-group">
            <label for="exampleInputEmail1">Email address</label>
            <input type="email" class="form-control" id="emailRegistration" placeholder="Enter email" value="romascudeto@yahoo.com">
        </div>
        <div class="form-group">
            <label for="exampleInputPassword1">Password</label>
            <input type="password" class="form-control" id="passwordRegistration" placeholder="Password" value="12345">
        </div>

        <div class="modal fade model-register-u2f" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        U2F Registration
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <h5>Please enter your FIDO U2F device into your computer's USB port. Then confirm registration on the device.</h5>
                    </div>
                </div>
            </div>
        </div>

        <button type="button" class="btn btn-primary" onClick="submitRegister()">Submit</button>
        <button type="button" class="btn btn-default" onClick="showLogin()">Login</button>
        </form>
    </div>
</div>
</body>
</html>