Vue.createApp({
    data() {
        return{
            signingout: false,          // True while loading
            loaded: false,              // True when loading complete
            error: false,               // True if there's an error
            errorMessage: ""            // Error message to display
        }
    },
    mounted () {

        this.signingout = true;

        fetch('./api/signout.php')
        .then(response => response.json())
        .then(data => this.PostDataLoad(data));
    },
    methods:{
        PostDataLoad(data){                             // Called after data load

            if (data.result == "error"){

            } else {                                    // No error - redirect after sign-out

                window.location.href = "./signin.html";
            }
        }
    }
}).mount('#app')