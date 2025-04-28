        <?php 
            require_once 'db.php';

            class User {
                private $db;

                public function __construct() {
                    $this->db = Database::getInstance();
                }

                public function register($name, $login, $password) {
                    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                    
                    $sql = "INSERT INTO users (name, login, password_hash) VALUES (?, ?, ?)";
                    $this->db->query($sql, [$name, $login, $passwordHash]);
                    
                    return $this->db->getConnection()->lastInsertId();
                }

                public function login($login, $password) {
                    $sql = "SELECT * FROM users WHERE login = ?";
                    $user = $this->db->query($sql, [$login])->fetch();
                    
                    if ($user && password_verify($password, $user['password_hash'])) {
                        return $user;
                    }
                    
                    return false;
                }

                public function getUserById($id) {
                    $sql = "SELECT * FROM users WHERE id = ?";
                    return $this->db->query($sql, [$id])->fetch();
                }

                public function updateUser($id, $name, $password = null) {
                    if ($password) {
                        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                        $sql = "UPDATE users SET name = ?, password_hash = ? WHERE id = ?";
                        $this->db->query($sql, [$name, $passwordHash, $id]);
                    } else {
                        $sql = "UPDATE users SET name = ? WHERE id = ?";
                        $this->db->query($sql, [$name, $id]);
                    }
                }

                public function isLoginExists($login) {
                    $sql = "SELECT COUNT(*) FROM users WHERE login = ?";
                    return $this->db->query($sql, [$login])->fetchColumn() > 0;
                }

                public function getFavorites($userId) {
                    $sql = "SELECT r.*, c.name as category_name 
                            FROM user_favorites uf 
                            JOIN recipes r ON uf.recipe_id = r.id 
                            JOIN categories c ON r.category_id = c.id 
                            WHERE uf.user_id = ? 
                            ORDER BY r.created_at DESC";
                    return $this->db->query($sql, [$userId])->fetchAll();
                }

                public function isFavorite($userId, $recipeId) {
                    $sql = "SELECT COUNT(*) FROM user_favorites WHERE user_id = ? AND recipe_id = ?";
                    return $this->db->query($sql, [$userId, $recipeId])->fetchColumn() > 0;
                }

                public function addFavorite($userId, $recipeId) {
                    if (!$this->isFavorite($userId, $recipeId)) {
                        $sql = "INSERT INTO user_favorites (user_id, recipe_id) VALUES (?, ?)";
                        $this->db->query($sql, [$userId, $recipeId]);
                    }
                }

                public function removeFavorite($userId, $recipeId) {
                    $sql = "DELETE FROM user_favorites WHERE user_id = ? AND recipe_id = ?";
                    $this->db->query($sql, [$userId, $recipeId]);
                }

                public function getFavoritesCount($userId) {
                    $sql = "SELECT COUNT(*) FROM user_favorites WHERE user_id = ?";
                    return $this->db->query($sql, [$userId])->fetchColumn();
                }
            }
        ?>
