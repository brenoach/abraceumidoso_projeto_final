-- =========================================
-- RESET COMPLETO DO BANCO (SEM APAGAR TABELAS)
-- =========================================

USE if0_41248576_bd_abraceumidoso;

-- Desativa verificação de chave estrangeira
SET FOREIGN_KEY_CHECKS = 0;

-- Apaga todos os dados (ordem correta)
DELETE FROM disponibilidade;
DELETE FROM agendamento;
DELETE FROM carta;
DELETE FROM funcionario;
DELETE FROM idoso;
DELETE FROM voluntario;
DELETE FROM instituicao;
DELETE FROM pessoa;
DELETE FROM endereco;
DELETE FROM contato;

-- Reseta AUTO_INCREMENT
ALTER TABLE contato AUTO_INCREMENT = 1;
ALTER TABLE endereco AUTO_INCREMENT = 1;
ALTER TABLE pessoa AUTO_INCREMENT = 1;
ALTER TABLE voluntario AUTO_INCREMENT = 1;
ALTER TABLE instituicao AUTO_INCREMENT = 1;
ALTER TABLE idoso AUTO_INCREMENT = 1;
ALTER TABLE carta AUTO_INCREMENT = 1;
ALTER TABLE funcionario AUTO_INCREMENT = 1;
ALTER TABLE agendamento AUTO_INCREMENT = 1;
ALTER TABLE disponibilidade AUTO_INCREMENT = 1;

-- Reativa verificação de chave estrangeira
SET FOREIGN_KEY_CHECKS = 1;

-- =========================================
-- BANCO LIMPO COM SUCESSO
-- =========================================