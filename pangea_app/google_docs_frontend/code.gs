function onOpen(e) {
      DocumentApp.getUi().createAddonMenu()
          .addItem('Start', 'showSidebar')
          .addToUi();
      showSidebar()
    }

    function onInstall(e) {
      onOpen(e);
    }

    function showSidebar() {
      var ui = HtmlService.createHtmlOutputFromFile('Sidebar')
          .setTitle('Sensitive Data Redaction For Google Docs')
          .setSandboxMode(HtmlService.SandboxMode.IFRAME);
      DocumentApp.getUi().showSidebar(ui);
    }