<style>
    #container-form-trabalhe{
        width: 85%;
        margin: 0 auto;
    }

    @media(min-width:1500px) {
        #container-form-trabalhe{
            width: 70%;
            margin: 0 auto;
        }
    }
    
    @media(max-width:992px) {
        #container-form-trabalhe{
            width: 90%;
            margin: 0 auto;
        }
    }
</style>


<section id="container-form-trabalhe">
    <div class="py-5 my-2 w-100">

        <div class="d-flex mb-5">
            <a href="<?= $loja['site']; ?>index.php" style="color: #A7A7A7;">Home</a>
            <span class="mx-2">/</span>
            <a href="<?= $loja['site']; ?>trabalhe-conosco.php" style="color: #DC582A;">Trabalhe Conosco</a>
        </div>

        <h3 class="mb-5" style="color: #5B5C5D;">Faça parte da <strong>nossa equipe</strong></h3>

        <form action="<?= $loja['site']; ?>modulos/trabalhe-conosco/php/enviar-curriculo.php" method="post" enctype="multipart/form-data" class="row">
            <div class='mb-4 col-12 col-md-6'>
              <label for='nome' style="color: #1C4A50;">Seu nome completo*</label>
              <input type='text' id='nome' name='nome' placeholder='Digite seu nome completo' class='form-control' required>
            </div>

            <div class='mb-4 col-12 col-md-6'>
              <label for='email' style="color: #1C4A50;">Seu E-mail*</label>
              <input type='email' id='email' name='email' placeholder='Digite seu E-mail' class='form-control' required>
            </div>

            <div class='mb-4 col-12 col-md-6'>
              <label for='numero' style="color: #1C4A50;">Seu Número*</label>
              <input type='tel' id='numero' name='numero' placeholder='Digite seu Número' class='form-control' inputmode="numeric" maxlength="16" required>
            </div>

            <div class='mb-4 col-12 col-md-6'>
              <label for='vaga' style="color: #1C4A50;">Vaga pretendida*</label>
              <select name="vaga" id="vaga" class="form-control" required>
                <option value="">Selecione a vaga</option>
                <option value="vaga1">Vaga 1</option>
                <option value="vaga2">Vaga 2</option>
                <option value="vaga3">Vaga 3</option>
              </select>
            </div>

            <div class='mb-4 col-12 col-md-6'>
              <label for='estado' style="color: #1C4A50;">Estado*</label>
              <input type='text' id='estado' name='estado' placeholder='Digite seu Estado' class='form-control' required>
            </div>

            <div class='mb-4 col-12 col-md-6'>
              <label for='cidade' style="color: #1C4A50;">Cidade*</label>
              <input type='text' id='cidade' name='cidade' placeholder='Digite sua Cidade' class='form-control' required>
            </div>

            <div class='mb-4 col-12'>
              <label for='mensagem' style="color: #1C4A50;">Mensagem*</label>
              <textarea id='mensagem' rows="3" name='mensagem' placeholder='Digite sua Mensagem' class='form-control' required></textarea>
            </div>

            <div class="px-3 px-lg-0 d-flex flex-column">
                <p class="mt-4 mb-4" style="color: #1C4A50; font-weight: bold;">Envie seu Curriculo (apenas arquivos em PDF)*</p>
                
                <div class='mb-4'>
                  <label style="color: #1C4A50;" for="curriculo">Tamanho máx. 2mb</label>
                  <input type='file' id='curriculo' name='curriculo' accept=".pdf" class='w-100 form-control' required>
                </div>
                
                <p style="color: #1C4A50; font-weight: 500;" class="mt-2 mb-4">Ao clicar em “Enviar” você automaticamente declara aceitar e consentir com o tratamento de dados pessoais enviados através deste formulário, bem como os anexos incluídos, também fazem parte desta declaração.</p>
            </div>

            <button type="submit" style="background-color: #DC582A; color: white; border: none; padding: 12px 20px; border-radius: 4px; cursor: pointer;">Enviar currículo</button>
        </form>
    </div>
</section>

<script>
  (function() {
    // Máscara de telefone: (##) 9 ####-####
    const telInput = document.getElementById('numero');
    function formatBRPhone(value) {
      const digits = (value || '').replace(/\D/g, '').slice(0, 11); // DDD(2) + 9(1) + número(8)
      const ddd = digits.slice(0, 2);
      const first = digits.slice(2, 3); // o 9
      const middle = digits.slice(3, 7);
      const last = digits.slice(7, 11);

      let out = '';
      if (ddd) {
        out += '(' + ddd;
        if (digits.length >= 3) out += ') ';
      }
      if (first) {
        out += first;
        if (middle) out += ' ' + middle;
      }
      if (last) {
        out += '-' + last;
      }
      return out;
    }

    function onPhoneInput(e) {
      const cursorPos = e.target.selectionStart;
      const before = e.target.value;
      const formatted = formatBRPhone(before);
      e.target.value = formatted;
      // Ajuste simples de cursor para evitar pular muito (best effort)
      try {
        const delta = formatted.length - before.length;
        e.target.setSelectionRange(Math.max(0, (cursorPos || 0) + delta), Math.max(0, (cursorPos || 0) + delta));
      } catch (err) {}
    }

    if (telInput) {
      telInput.addEventListener('input', onPhoneInput);
      telInput.addEventListener('blur', onPhoneInput);
      // Formatar o valor inicial se vier preenchido
      telInput.value = formatBRPhone(telInput.value);
    }

    // Validação do PDF <= 2MB
    const MAX_BYTES = 2 * 1024 * 1024; // 2MB
    const fileInput = document.getElementById('curriculo');
    const form = document.querySelector("form[action$='modulos/trabalhe-conosco/php/enviar-curriculo.php']");

    function isValidPdf(file) {
      if (!file) return false;
      const isPdfByMime = file.type === 'application/pdf';
      const isPdfByExt = file.name && file.name.toLowerCase().endsWith('.pdf');
      return (isPdfByMime || isPdfByExt) && file.size <= MAX_BYTES;
    }

    function validateFileOrWarn(inputEl) {
      const file = inputEl.files && inputEl.files[0];
      if (!file) return true; // sem arquivo, deixa form tratar required
      const isPdfMime = file.type === 'application/pdf' || (file.name && file.name.toLowerCase().endsWith('.pdf'));
      if (!isPdfMime) {
        alert('Por favor, envie um arquivo PDF (.pdf).');
        inputEl.value = '';
        return false;
      }
      if (file.size > MAX_BYTES) {
        alert('O arquivo selecionado excede 2 MB. Escolha um PDF menor.');
        inputEl.value = '';
        return false;
      }
      return true;
    }

    if (fileInput) {
      fileInput.addEventListener('change', function(e) {
        validateFileOrWarn(e.target);
      });
    }

    if (form) {
      form.addEventListener('submit', function(e) {
        // Revalida no submit para garantir
        if (fileInput && !validateFileOrWarn(fileInput)) {
          e.preventDefault();
          e.stopPropagation();
          return false;
        }
        // Opcional: garantir telefone completo (16 chars)
        if (telInput && telInput.value.replace(/\D/g, '').length !== 11) {
          // Não bloquear se não solicitado; apenas normalizar
          telInput.value = formatBRPhone(telInput.value);
        }
      });
    }
  })();
</script>


