module.exports = {
    HEADER: {
        'content-type': 'application/json'
    },
	// #ifdef H5
    baseURL: process.env.NODE_ENV == 'production' ? '/public/index.php/api/' : '/public/index.php/api/',
    // baseURL: process.env.NODE_ENV == 'production' ? '/api/' : '/api/',
	// #endif
	// #ifndef H5
	baseURL: '/public/index.php/api/'
	// #endif
}