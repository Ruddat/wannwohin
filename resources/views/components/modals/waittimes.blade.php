<div class="modal fade" id="waitTimeModal" tabindex="-1" aria-labelledby="waitTimeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-dark text-white">
          <h5 class="modal-title" id="waitTimeModalLabel">Wartezeiten</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Schließen"></button>
        </div>
        <div class="modal-body">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Attraktion</th>
                <th>Wartezeit</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody id="waitTimeTableBody">
              <!-- wird via JS gefüllt -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
