Vue.createApp({
    data() {
        return{
            registering: false
        }
    },
    mounted () {
    },
    methods:{
        Register(){

            $('#error-message').addClass("hidden");

            // Check that passwords match
            if ( $('#password').val() == $('#passwordconf').val() ){

                this.registering = true;

                let url = new URL(window.location);
                let invitetoken = url.searchParams.get("token");

                fetch('./api/register.php', {
                    method: "POST",
                    headers: {'Content-Type': 'application/json'}, 
                    body: '{"username": "' + $('#username').val() + '", "password": "' + $('#password').val() + '", "token": "' + invitetoken + '"}'
                })
                .then(response => response.json())
                .then(data => this.SignInResult(data));

            }
            else{
                console.log("No match");

                // Display the error message
                $('#error-message').html("Passwords don't match");
                $('#error-message').removeClass("hidden");
            }
            
        },
        SignInResult(data){
            this.registering = false;

            if (data.result == "success"){
                // Redirect to main app on log-in success        
                window.location.href = "./signin.html";
            }
            else{
                // Display the error message
                $('#error-message').html(data.message);
                $('#error-message').removeClass("hidden");
                console.log(data);
            }
        }
    }
}).mount('#app')