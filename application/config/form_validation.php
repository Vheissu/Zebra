<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config = array(
        'login' => array(
            array(
                'field' => 'username',
                'label' => 'Username',
                'rules' => 'required|alpha_dash'
            ),
            array(
                'password' => 'password',
                'label'    => 'Password',
                'rules'    => 'required'
            )
        ),
        'register' => array(
            array(
                'field' => 'username',
                'label' => 'Username',
                'rules' => 'required|alpha_dash'
            ),
            array(
                'field' => 'email',
                'label' => 'Email',
                'rules' => 'required|valid_email'
            ),
            array(
                'password' => 'password',
                'label'    => 'Password',
                'rules'    => 'required'
            )
        ),
        'story' => array(
            array(
                'field' => 'title',
                'label' => 'Title',
                'rules' => 'required|trim'
            ),
            array(
                'field' => 'link',
                'label' => 'Link',
                'rules' => 'prep_url|trim'
            ),
            array(
                'field' => 'text',
                'label' => 'Text',
                'rules' => 'trim'
            )
        )
);