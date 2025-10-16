<?php
// Charset e sessão
header('Content-Type: text/html; charset=UTF-8');
setlocale(LC_ALL,'pt_BR.UTF8');
mb_internal_encoding('UTF8');
mb_regex_encoding('UTF8');
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Função utilitária
function sanitize($value) {
	return trim(strip_tags($value));
}

// Coleta de dados do formulário
$nome     = sanitize(filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING));
$email    = sanitize(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
$numero   = sanitize(filter_input(INPUT_POST, 'numero', FILTER_SANITIZE_STRING));
$vaga     = sanitize(filter_input(INPUT_POST, 'vaga', FILTER_SANITIZE_STRING));
$estado   = sanitize(filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_STRING));
$cidade   = sanitize(filter_input(INPUT_POST, 'cidade', FILTER_SANITIZE_STRING));
$mensagem = sanitize(filter_input(INPUT_POST, 'mensagem', FILTER_SANITIZE_STRING));

// Validação simples dos obrigatórios
if (!$nome || !$email || !$numero || !$vaga || !$estado || !$cidade || !$mensagem || empty($_FILES['curriculo'])) {
	echo "<script>alert('Preencha todos os campos obrigatórios.'); history.back();</script>";
	exit;
}

// Validação do arquivo
$file = $_FILES['curriculo'] ?? null;
if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
	echo "<script>alert('Falha ao enviar o arquivo. Tente novamente.'); history.back();</script>";
	exit;
}

// Tamanho máximo 2MB
$MAX_BYTES = 2 * 1024 * 1024;
if ($file['size'] > $MAX_BYTES) {
	echo "<script>alert('O arquivo excede 2 MB. Escolha um PDF menor.'); history.back();</script>";
	exit;
}

// Verificação de tipo PDF por MIME e extensão
$filename = $file['name'];
$tmpPath  = $file['tmp_name'];
$extOk    = (strtolower(pathinfo($filename, PATHINFO_EXTENSION)) === 'pdf');
$mime     = mime_content_type($tmpPath);
$mimeOk   = in_array($mime, ['application/pdf', 'application/x-pdf', 'binary/octet-stream']); // permissivo
if (!$extOk || !$mimeOk) {
	echo "<script>alert('Envie apenas arquivos no formato PDF.'); history.back();</script>";
	exit;
}

// Busca configs da loja para SMTP e e-mails
include '../../../bd/conecta.php';
$busca_dados_loja = mysqli_query($conn, "SELECT * FROM loja WHERE id = 1");
$loja = mysqli_fetch_array($busca_dados_loja);
include '../../../bd/desconecta.php';

// Assuntos e corpos
$assunto_loja    = 'Novo currículo recebido (Trabalhe Conosco)';
$assunto_cliente = 'Recebemos seu currículo';

$corpo_loja = '
<table width="100%" cellpadding="10" cellspacing="0" style="font-family:Arial,Helvetica,sans-serif;color:#333;">
  <tr><td colspan="2" style="font-size:18px;font-weight:bold;">Novo currículo recebido</td></tr>
  <tr><td style="width:180px;font-weight:bold;">Nome</td><td>'.htmlspecialchars($nome, ENT_QUOTES, 'UTF-8').'</td></tr>
  <tr><td style="font-weight:bold;">E-mail</td><td>'.htmlspecialchars($email, ENT_QUOTES, 'UTF-8').'</td></tr>
  <tr><td style="font-weight:bold;">Telefone</td><td>'.htmlspecialchars($numero, ENT_QUOTES, 'UTF-8').'</td></tr>
  <tr><td style="font-weight:bold;">Vaga pretendida</td><td>'.htmlspecialchars($vaga, ENT_QUOTES, 'UTF-8').'</td></tr>
  <tr><td style="font-weight:bold;">Estado</td><td>'.htmlspecialchars($estado, ENT_QUOTES, 'UTF-8').'</td></tr>
  <tr><td style="font-weight:bold;">Cidade</td><td>'.htmlspecialchars($cidade, ENT_QUOTES, 'UTF-8').'</td></tr>
  <tr><td style="font-weight:bold;">Mensagem</td><td>'.nl2br(htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8')).'</td></tr>
  <tr><td style="font-weight:bold;">Anexo</td><td>'.htmlspecialchars($filename, ENT_QUOTES, 'UTF-8').'</td></tr>
</table>';

$corpo_cliente = '
<div style="font-family:Arial,Helvetica,sans-serif;color:#333;">
  <p>Olá '.htmlspecialchars($nome, ENT_QUOTES, 'UTF-8').',</p>
  <p>Recebemos seu currículo e agradecemos o seu interesse em fazer parte da nossa equipe.</p>
  <p>Em breve entraremos em contato caso seu perfil esteja alinhado com nossas vagas.</p>
  <p style="margin-top:16px;">Atenciosamente,<br>'.htmlspecialchars($loja['nome'] ?? 'Equipe', ENT_QUOTES, 'UTF-8').'</p>
</div>';

// Carrega PHPMailer (coloque a pasta PHPMailer nesta mesma pasta php/)
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$erroEnvio = '';

// Envia para loja com anexo
try {
	$mailLoja = new PHPMailer(true);
	if (isset($loja['email_issmtp']) && (int)$loja['email_issmtp'] === 1) {
		$mailLoja->isSMTP();
	}
	$mailLoja->isHTML(true);
	$mailLoja->SMTPDebug = 0;
	$mailLoja->SMTPAuth = true;
	$mailLoja->SMTPSecure = 'ssl';
	$mailLoja->Host = $loja['email_sistema_host'] ?? '';
	$mailLoja->Port = (int)($loja['email_sistema_porta'] ?? 0);
	$mailLoja->Username = $loja['email_sistema'] ?? '';
	$mailLoja->Password = $loja['email_sistema_senha'] ?? '';

	$mailLoja->setFrom($loja['email_sistema'] ?? $email, $loja['nome'] ?? 'Site');
	if (!empty($email)) { $mailLoja->addReplyTo($email, $nome); }
	$destinoLoja = $loja['email'] ?? '';
	if (!$destinoLoja) { throw new Exception('E-mail da loja não configurado.'); }
	$mailLoja->addAddress($destinoLoja);

	// CC adicionais, se houver
	if (!empty($loja['email_adicional'])) {
		$emails_adicionais = explode(',', $loja['email_adicional']);
		foreach ($emails_adicionais as $e) {
			$e = trim($e);
			if ($e !== '') $mailLoja->addCC($e);
		}
	}

	$mailLoja->Subject = $assunto_loja;
	$mailLoja->Body = $corpo_loja;
	$mailLoja->CharSet = 'UTF-8';

	// Anexa o PDF (via string para não depender do arquivo temporário)
	$pdfData = @file_get_contents($tmpPath);
	if ($pdfData === false) {
		throw new Exception('Não foi possível ler o arquivo enviado.');
	}
	$mailLoja->addStringAttachment($pdfData, $filename, 'base64', 'application/pdf');

	$mailLoja->send();
} catch (Exception $e) {
	$erroEnvio = 'Erro ao enviar para a loja: ' . $e->getMessage();
}

// Envia confirmação ao candidato (sem anexo)
try {
	$mailCli = new PHPMailer(true);
	if (isset($loja['email_issmtp']) && (int)$loja['email_issmtp'] === 1) {
		$mailCli->isSMTP();
	}
	$mailCli->isHTML(true);
	$mailCli->SMTPDebug = 0;
	$mailCli->SMTPAuth = true;
	$mailCli->SMTPSecure = 'ssl';
	$mailCli->Host = $loja['email_sistema_host'] ?? '';
	$mailCli->Port = (int)($loja['email_sistema_porta'] ?? 0);
	$mailCli->Username = $loja['email_sistema'] ?? '';
	$mailCli->Password = $loja['email_sistema_senha'] ?? '';

	$mailCli->setFrom($loja['email_sistema'] ?? $email, $loja['nome'] ?? 'Equipe');
	$mailCli->addAddress($email, $nome);
	$mailCli->Subject = $assunto_cliente;
	$mailCli->Body = $corpo_cliente;
	$mailCli->CharSet = 'UTF-8';
	$mailCli->send();
} catch (Exception $e) {
	// Não bloqueia se falhar a mensagem ao cliente
}

// Retorno ao usuário
if ($erroEnvio) {
	echo "<script>alert('".addslashes($erroEnvio)."'); history.back();</script>";
	exit;
}

$retorno = !empty($loja['site']) ? rtrim($loja['site'], '/').'/trabalhe-conosco.php' : '/trabalhe-conosco.php';
echo "<script>alert('Currículo enviado com sucesso!'); location.href='".addslashes($retorno)."';</script>";
exit;
