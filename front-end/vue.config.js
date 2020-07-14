// vue.config.js
module.exports = {
    // options...
    devServer: {
        disableHostCheck: true,
        proxy: 'http://api.webchat.com:8081',
        // https: true
    }
}