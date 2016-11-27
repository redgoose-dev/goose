/**
 * Object to Array
 * object형식의 데이더를 array형식의 데이터로 변환시킨다. (only 1depth)
 *
 * @param {Object} obj
 * @return {Array}
 */
const objectToArray = function(obj)
{
	var result = [];
	for (var o in obj)
	{
		result.push(obj[o]);
	}
	return result;
};

/**
 * Get Last Item
 * 배열에서 마지막 요소를 가져옵니다.
 *
 * @param {Array} arr
 * @return {*}
 */
const getLastItem = function(arr)
{
	return arr[arr.length-1];
};

/**
 * Get Last Item
 * 중첩되어있는 배열의 특정값을 다른 하나의 배열에 나란히 담는다.
 *
 * @param {Array} src 소스배열
 * @param {Array} out 출력되는 배열
 * @param {String} key_target 가져오려는 key 이름
 * @param {String} key_child 자식 key 이름
 * @return {*}
 */
const setArrayItem = function(src, out, key_target, key_child)
{
	for (var i=0; i<src.length; i++)
	{
		if (src[i][key_target])
		{
			out.push(src[i][key_target]);
		}
		if (src[i][key_child])
		{
			setArrayItem(src[i][key_child], out, key_target, key_child);
		}
	}
};


export {
	objectToArray,
	getLastItem,
	setArrayItem
};