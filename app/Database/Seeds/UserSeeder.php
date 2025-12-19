<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
	public function run()
	{
		$users = [
			[
				'username' => 'admin',
				'password' => password_hash('admin123', PASSWORD_DEFAULT),
				'role'     => 'admin',
			],
		];

		// Insert users only if they don't already exist
		foreach ($users as $user) {
			$existing = $this->db->table('users')
				->where('username', $user['username'])
				->countAllResults();

			if ($existing === 0) {
				$this->db->table('users')->insert($user);
				echo "✓ Created user: {$user['username']}\n";
			} else {
				echo "- User already exists: {$user['username']}\n";
			}
		}
	}
}
