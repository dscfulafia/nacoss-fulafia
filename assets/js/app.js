var example1=new Vue({
  el: '#post',
  data () {
    return {
      posts: null,
      loading: true,
      errored: false
    }
  },
  methods: {
    shortenedPostContent (value) {
        //stripping any html content in the value
        var htmlContent = value
        var virtualDiv = document.createElement("div")
        virtualDiv.innerHTML = value
      return virtualDiv.innerText.substring(0,300)+"....";
    },
    getImage (imageURL) {
      return (imageURL == null) ? "assets/gallery/no-image.jpg" : imageURL;
    }

  },
  mounted () {
    axios
      .get(API_URL + '?call=getallposts')
      .then(response => {
        this.posts = response.data.data.body
        this.errored =  response.data.data.errorExist
      })
      .catch(error => {
        console.log(error)
        this.errored = true
      })
      .finally(() => this.loading = false)
  }
})