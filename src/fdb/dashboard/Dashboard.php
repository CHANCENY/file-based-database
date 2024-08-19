<?php

namespace Fdb\dashboard;

use Fdb\Connect\Connect;

class Dashboard
{
    private array $post_array;

    private array $get_array;

    private array $server_array;

    private array $session_array;
    private Connect $connection;
    private mixed $json_array;

    /**
     * @param array $post_array $_POST
     * @param array $get_array $_GET
     * @param array $server_array $_SERVER
     * @param array $session_array $_SESSION
     */
    public function __construct(array $post_array, array $get_array, array $server_array, array $session_array)
    {

        $this->post_array = $post_array;
        $this->get_array = $get_array;
        $this->server_array = $server_array;
        $this->session_array = $session_array;

        // Initialize dashboard on request.

        $this->init();

        $this->json_array = json_decode(file_get_contents('php://input'), true);
        // Removing collection-key
        $this->postRequestRemoveCollectionKey();

        $this->postListingData();
        require_once __DIR__.'/index.php';
    }

    private function init(): void
    {
        global $database_name;
        global $database_username;
        global $database_password;
        global $database;

        $database_name = $this->session_array['dbname'] ?? $this->post_array['database_name'] ?? null;
        $database_username = $this->session_array['dbuser'] ?? $this->post_array['username'] ?? null;
        $database_password = $this->session_array['dbpassword'] ?? $this->post_array['password'] ?? null;

        if(!empty($database_name) && !empty($database_password) && !empty($database_username)) {
            $this->connection = new Connect($database_name, $database_username, $database_password);
            $_SESSION['dbname'] = $database_name;
            $_SESSION['dbuser'] = $database_username;
            $_SESSION['dbpassword'] = $database_password;
            $database = $this->connection;
        }
    }

    private function postRequestRemoveCollectionKey(): void
    {
        if($this->server_array['REQUEST_METHOD'] === 'POST' && $this->json_array['action'] === 'remove-collection-key')
        {
            $result = $this->connection->removeCollectionKey($this->json_array['collection_name'], $this->json_array['collection_key']);
            if($result) {
                http_response_code(200);
                header('Content-Type: application/json');
                echo json_encode(['status'=> true]);
            }
            else {
                http_response_code(404);
                header('Content-Type: application/json');
                echo json_encode(['status'=> false]);
            }
            exit;
        }
    }

    private function postListingData()
    {
        if($this->server_array['REQUEST_METHOD'] === 'POST' && $this->json_array['action'] === 'listing-data') {
            $data = $this->connection->select->all($this->json_array['collection_name']);
            header('Content-Type: application/json');
            if(!empty($data)) {
                http_response_code(200);
                echo json_encode($data,JSON_PRETTY_PRINT);
                exit;
            }

            http_response_code(200);
            echo json_encode([]);
            exit;
        }
    }

    function arrayToHtmlTable($array) {
        $html = '<table border="1">';

        foreach ($array as $key => $value) {
            $html .= '<tr>';
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
            if (is_array($value)) {
                $html .= '<td>' . $this->arrayToHtmlTable($value) . '</td>'; // Recursively handle nested arrays
            } else {
                $html .= '<td>' . htmlspecialchars($value) . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</table>';

        return $html;
    }
}