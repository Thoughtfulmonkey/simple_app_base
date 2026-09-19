Vue.createApp({
    data() {
        return{
            loading: false,             // True while loading
            loaded: false,              // True when loading complete
            error: false,               // True if there's an error
            errorMessage: ""            // Error message to display
        }
    },
    mounted () {

        this.loading = true;

        fetch('./api/base.php')
        .then(response => response.json())
        .then(data => this.PostDataLoad(data));
    },
    methods:{
        PostDataLoad(data){                             // Called after data load

            if (data.result){
                if (data.result == "error"){
                    window.location.href = "./signin.html";
                }

            } else {                                    // No error - load the data

                //this.listing = data;

                this.loading = false;
                this.loaded = true;
            }
        }
    }
}).mount('#app')