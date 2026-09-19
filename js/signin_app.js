Vue.createApp({
    data() {
        return{
            signingIn: false
        }
    },
    mounted () {
    },
    methods:{
        SignIn(){
            this.signingIn = true;

            fetch('./api/signin.php', {
                method: "POST",
                headers: {'Content-Type': 'application/json'}, 
                body: '{"username": "' + $('#username').val() + '", "password": "' + $('#password').val() + '"}'
            })
            .then(response => response.json())
            .then(data => this.SignInResult(data));
        },
        SignInResult(data){
            this.signingIn = false;

            if (data.result == "success"){
                // Redirect to main app on log-in success        
                window.location.href = "./index.html";
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
