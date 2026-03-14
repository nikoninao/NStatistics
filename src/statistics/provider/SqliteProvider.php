<?php

declare(strict_types=1);

namespace statistics\provider;

use SQLite3;
use statistics\model\PlayerStats;

class SqliteProvider implements StatsProvider {

    private SQLite3 $db;

    public function __construct(string $folder) {
        $path = $folder . 'stats.db';
        $this->db = new SQLite3($path);
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS player_stats (
                player TEXT PRIMARY KEY COLLATE NOCASE,
                kills INTEGER NOT NULL DEFAULT 0,
                deaths INTEGER NOT NULL DEFAULT 0
            );
        ");
    }

    public function load(string $player): PlayerStats {
        $stmt = $this->db->prepare('
            SELECT kills, deaths 
            FROM player_stats 
            WHERE player = :player
        ');
        $stmt->bindValue(':player', strtolower($player), SQLITE3_TEXT);
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_ASSOC);
        if ($row === false) {
            return new PlayerStats(0, 0);
        }
        return new PlayerStats(
            (int) $row['kills'],
            (int) $row['deaths'],
        );
    }

    public function save(string $player, PlayerStats $stats): void {
        $stmt = $this->db->prepare('
            INSERT OR REPLACE INTO player_stats 
            (player, kills, deaths) 
            VALUES (:player, :kills, :deaths)
        ');

        $stmt->bindValue(':player', strtolower($player));
        $stmt->bindValue(':kills',   $stats->kills,   SQLITE3_INTEGER);
        $stmt->bindValue(':deaths',  $stats->deaths,  SQLITE3_INTEGER);

        $stmt->execute();
    }

    public function __destruct() {
        $this->db->close();
    }
}