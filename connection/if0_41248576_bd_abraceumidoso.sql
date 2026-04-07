USE if0_41248576_bd_abraceumidoso;

CREATE TABLE contato(
idContato INT PRIMARY KEY AUTO_INCREMENT,
email VARCHAR(50) NOT NULL,
celular CHAR(11),
telefone VARCHAR(11)
);

CREATE TABLE endereco(
idEndereco INT PRIMARY KEY AUTO_INCREMENT,
cep CHAR(8) NOT NULL,
estado CHAR(2) NOT NULL,
cidade VARCHAR(50) NOT NULL,
bairro VARCHAR(50) NOT NULL,
numero CHAR(6) NOT NULL,
complemento VARCHAR(50),
nomeLogradouro VARCHAR(50),
tipoLogradouro VARCHAR(15)
);

CREATE TABLE pessoa(
idPessoa INT PRIMARY KEY AUTO_INCREMENT,
nomePessoa VARCHAR(50) NOT NULL,
cpf CHAR(11) NOT NULL UNIQUE,
dataNascimento DATE NOT NULL,
fotoPerfil VARCHAR(250) NOT NULL,
sobre VARCHAR(250) NOT NULL
);

CREATE TABLE voluntario(
idVoluntario INT PRIMARY KEY AUTO_INCREMENT,
senha VARCHAR(255) NOT NULL,
idContato INT NOT NULL,
idEndereco INT NOT NULL,
idPessoa INT NOT NULL,
resetToken VARCHAR(255) ,
tokenExpira DATETIME,
CONSTRAINT fk_voluntario_contato FOREIGN KEY (idContato) REFERENCES contato(idContato),
CONSTRAINT fk_voluntario_endereco FOREIGN KEY (idEndereco) REFERENCES endereco(idEndereco),
CONSTRAINT fk_voluntario_pessoa FOREIGN KEY (idPessoa) REFERENCES pessoa(idPessoa)
);

CREATE TABLE instituicao(
idInstituicao INT PRIMARY KEY AUTO_INCREMENT,
nomeInstituicao VARCHAR(50) NOT NULL,
fotoInstituicao VARCHAR(250) NOT NULL,
cnpj VARCHAR(14) NOT NULL UNIQUE,
senha VARCHAR(255) NOT NULL,
idContato INT NOT NULL,
idEndereco INT NOT NULL,
CONSTRAINT fk_instituicao_contato FOREIGN KEY (idContato) REFERENCES contato(idContato),
CONSTRAINT fk_instituicao_endereco FOREIGN KEY (idEndereco) REFERENCES endereco(idEndereco)
);

CREATE TABLE idoso(
idIdoso INT PRIMARY KEY AUTO_INCREMENT,
idPessoa INT NOT NULL,
idInstituicao INT NOT NULL,
aceitaVisita TINYINT DEFAULT 1,
aceitaCarta TINYINT DEFAULT 1,
CONSTRAINT fk_idoso_pessoa FOREIGN KEY (idPessoa) REFERENCES pessoa(idPessoa),
CONSTRAINT fk_idoso_instituicao FOREIGN KEY (idInstituicao) REFERENCES instituicao(idInstituicao)
);


CREATE TABLE carta(
idCarta INT PRIMARY KEY AUTO_INCREMENT,
textoCarta TEXT NOT NULL,
dataCarta DATETIME NOT NULL,
statusCarta CHAR(10) NOT NULL,
idVoluntario INT NOT NULL,
idIdoso INT NOT NULL,
CONSTRAINT fk_carta_voluntario FOREIGN KEY (idVoluntario) REFERENCES voluntario(idVoluntario),
CONSTRAINT fk_carta_idoso FOREIGN KEY (idIdoso) REFERENCES idoso(idIdoso)
);

CREATE TABLE funcionario (
idFuncionario INT AUTO_INCREMENT PRIMARY KEY,
cargo VARCHAR(50) NOT NULL,
senha VARCHAR(255) NOT NULL,
idPessoa INT NOT NULL,
idInstituicao INT NOT NULL,
idContato INT NOT NULL,
CONSTRAINT fk_funcionario_pessoa FOREIGN KEY (idPessoa) REFERENCES pessoa(idPessoa),
CONSTRAINT fk_funcionario_instituicao FOREIGN KEY (idInstituicao) REFERENCES instituicao(idInstituicao),
CONSTRAINT fk_funcionario_contato FOREIGN KEY (idContato ) REFERENCES contato(idContato)
);

CREATE TABLE agendamento(
    idAgendamento INT PRIMARY KEY AUTO_INCREMENT,
    dataAgendamento DATE NOT NULL,
    horaAgendamento TIME NOT NULL,
    idVoluntario INT NOT NULL,
    idIdoso INT NOT NULL,
    status VARCHAR(50) DEFAULT 'Pendente',
    CONSTRAINT fk_agendamento_voluntario FOREIGN KEY (idVoluntario) REFERENCES voluntario(idVoluntario),
    CONSTRAINT fk_agendamento_idoso FOREIGN KEY (idIdoso) REFERENCES idoso(idIdoso)
); 


CREATE TABLE disponibilidade(
    idDisponibilidade INT AUTO_INCREMENT PRIMARY KEY,
    idoso_idIdoso INT NOT NULL, 
    dia_semana ENUM('Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado', 'Domingo') NOT NULL,    
    hora_inicio TIME NOT NULL,
    hora_fim TIME NOT NULL,
    CONSTRAINT fk_disponibilidade_idoso 
        FOREIGN KEY (idoso_idIdoso) 
        REFERENCES idoso(idIdoso) 
        ON DELETE CASCADE,
    CONSTRAINT check_horario_valido 
        CHECK (hora_fim > hora_inicio)
);

