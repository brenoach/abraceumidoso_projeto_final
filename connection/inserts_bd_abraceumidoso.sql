USE if0_41248576_bd_abraceumidoso;

-- =========================
-- CONTATO (emails para login)
-- =========================
INSERT INTO contato (email, celular, telefone) VALUES
('joao.voluntario@gmail.com','11999999901','1130000001'),
('maria.voluntario@gmail.com','11999999902','1130000002'),
('carlos.voluntario@gmail.com','11999999903','1130000003'),

('amparo.instituicao@gmail.com','11999999904','1130000004'),
('vida.instituicao@gmail.com','11999999905','1130000005'),
('luz.instituicao@gmail.com','11999999906','1130000006'),

('ana.funcionario@gmail.com','11999999907','1130000007'),
('paulo.funcionario@gmail.com','11999999908','1130000008'),
('clara.funcionario@gmail.com','11999999909','1130000009');

-- =========================
-- ENDERECO
-- =========================
INSERT INTO endereco VALUES
(NULL,'11000000','SP','Santos','Centro','10',NULL,'Rua A','Rua'),
(NULL,'11000001','SP','Santos','Centro','20',NULL,'Rua B','Rua'),
(NULL,'11000002','SP','Santos','Centro','30',NULL,'Rua C','Rua'),
(NULL,'11000003','SP','Santos','Centro','40',NULL,'Rua D','Rua'),
(NULL,'11000004','SP','Santos','Centro','50',NULL,'Rua E','Rua'),
(NULL,'11000005','SP','Santos','Centro','60',NULL,'Rua F','Rua'),
(NULL,'11000006','SP','Santos','Centro','70',NULL,'Rua G','Rua'),
(NULL,'11000007','SP','Santos','Centro','80',NULL,'Rua H','Rua'),
(NULL,'11000008','SP','Santos','Centro','90',NULL,'Rua I','Rua');

-- =========================
-- PESSOA
-- =========================
INSERT INTO pessoa VALUES
(NULL,'João Silva','11111111111','1990-01-01','foto.jpg','Voluntário dedicado'),
(NULL,'Maria Souza','22222222222','1992-02-02','foto.jpg','Ama ajudar idosos'),
(NULL,'Carlos Lima','33333333333','1988-03-03','foto.jpg','Sempre disposto'),

(NULL,'Ana Paula','44444444444','1985-04-04','foto.jpg','Funcionária atenciosa'),
(NULL,'Paulo Mendes','55555555555','1980-05-05','foto.jpg','Responsável'),
(NULL,'Clara Nunes','66666666666','1995-06-06','foto.jpg','Organizada'),

(NULL,'Dona Maria','77777777777','1940-07-07','foto.jpg','Idosa simpática'),
(NULL,'Seu José','88888888888','1938-08-08','foto.jpg','Gosta de conversar'),
(NULL,'Dona Ana','99999999999','1945-09-09','foto.jpg','Muito alegre');

-- =========================
-- INSTITUICAO
-- =========================
INSERT INTO instituicao VALUES
(NULL,'Lar Amparo','foto.jpg','11111111111111','123',4,4),
(NULL,'Casa Vida','foto.jpg','22222222222222','123',5,5),
(NULL,'Lar Luz','foto.jpg','33333333333333','123',6,6);

-- =========================
-- VOLUNTARIO
-- =========================
INSERT INTO voluntario VALUES
(NULL,'123',1,1,1,NULL,NULL),
(NULL,'123',2,2,2,NULL,NULL),
(NULL,'123',3,3,3,NULL,NULL);

-- =========================
-- FUNCIONARIO
-- =========================
INSERT INTO funcionario VALUES
(NULL,'Cuidador','123',4,1,7),
(NULL,'Administrador','123',5,2,8),
(NULL,'Auxiliar','123',6,3,9);

-- =========================
-- IDOSO
-- =========================
INSERT INTO idoso VALUES
(NULL,7,1,1,1),
(NULL,8,2,1,1),
(NULL,9,3,1,1);

-- =========================
-- CARTA
-- =========================
INSERT INTO carta VALUES
(NULL,'Olá Dona Maria, espero que esteja bem!','2026-01-01 10:00:00','Enviada',1,1),
(NULL,'Bom dia Seu José, tudo bem?','2026-01-02 11:00:00','Enviada',2,2),
(NULL,'Boa tarde Dona Ana, com carinho!','2026-01-03 12:00:00','Enviada',3,3);

-- =========================
-- AGENDAMENTO
-- =========================
INSERT INTO agendamento VALUES
(NULL,'2026-02-01','10:00:00',1,1,'Pendente'),
(NULL,'2026-02-02','11:00:00',2,2,'Confirmado'),
(NULL,'2026-02-03','12:00:00',3,3,'Pendente');

-- =========================
-- DISPONIBILIDADE
-- =========================
INSERT INTO disponibilidade VALUES
(NULL,1,'Segunda-feira','09:00:00','12:00:00'),
(NULL,2,'Terça-feira','10:00:00','13:00:00'),
(NULL,3,'Quarta-feira','14:00:00','17:00:00');