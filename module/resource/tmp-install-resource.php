<?php
/**
 * Install content
 *
 * @param array $post
 */
public static function installContent($post)
{
	/*
		check $post
		check ftp
		copy
	*/

	try
	{

		$dest_loc = explode('/', $post['location']);
		$dest_name = array_pop($dest_loc);
		$dest_loc = implode('/', $dest_loc).'/';

		// check file
		if (!file_exists(__GOOSE_PWD__.$post['file']))
		{
			throw new Exception('not found install file');
		}

		// check location
		if (!is_dir(__GOOSE_PWD__.$dest_loc))
		{
			throw new Exception('not found directory');
		}

		// load ftp setting file
		var_dump(__GOOSE_PWD__.'data/')

		}
	catch(Exception $e)
	{
		return [
			'state' => 'error',
			'message' => $e->getMessage(),
		];
	}
}


case 'install':
	header('Access-Control-Allow-Origin: *');
	if (ResourceAPI::checkAuthHeader())
	{
		$result = ResourceAPI::installContent([
			'file' => $_POST['install_file'],
			'location' => str_replace('{GOOSE}/', '', $_POST['pwd']),
		]);
	}
	else
	{
		$result = [
			'state' => 'error',
			'message' => 'Path not allowed'
		];
	}
	break;