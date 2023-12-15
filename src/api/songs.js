import request from '../utils/request';
 
//请求文件
export function getFolderFile(params={}) {
    return request({
        methods:'GET',
        url:'/api.php',
        params,
    })
}

// export function getList(params={}) {
//     return request({
//         methods:'GET',
//         url:'/products',
//         params,
//     })
// }
 
// export function getProduct(id) {
//     return request({
//         methods:'GET',
//         url:'/products/${id}',
//     })
// }
// export function update(id,data) {
//     return request({
//         methods:'PUT',
//         url:'/products/${id}',
//         data,
//     })
// }