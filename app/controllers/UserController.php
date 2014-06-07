<?php
class UserController extends ControllerBase {
	
	public function registerAction() {
		if ($this->request->isPost()) {
			$user = new Users();

			$user->assign(array(
				'username' => $this->request->getPost('username', 'striptags'),
				'email'    => $this->request->getPost('email', 'email'),
				'password' => $this->request->getPost('password'),
			));

			// set response to json
            $this->setJsonResponse();

			if ($user->save()) {
				$this->response->setStatusCode(201, "Created");

				return array('status' => 'OK', 'data' => $user);
			} else {
				$this->response->setStatusCode(409, "Conflict");

                // send errors to client
                $errors = array();
                foreach ($user->getMessages() as $message) {
                    $errors[] = $message->getMessage();
                }

                return array('status' => 'ERROR', 'message' => $errors);
			}
		}
	}

	public function loginAction() {
		if ($this->request->isPost()) {
			$username = $this->request->getPost('username');
			$password = $this->request->getPost('password');

			$password = sha1($password);

			$user = Users::findFirst(array(
				'username = :username: AND password = :password:',
				'bind' => array('username' => $username, 'password' => $password)
			));

			$user->setAccessToken();
			$user->save();

			$this->setJsonResponse();

			if ($user != false) {
				$this->response->setStatusCode(201, "LogedIn");

				return array('status' => 'OK', 'data' => $user);
			} else {
				$this->response->setStatusCode(409, "Conflict");

				return array('status' => 'ERROR', 'message' => array('username or password is invalid'));
			}
		}
	}
}