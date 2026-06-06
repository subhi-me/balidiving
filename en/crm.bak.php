<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>CRM Scuba Diving — BALI DIVING</title>
  <script src="/_sdk/element_sdk.js"></script>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body{box-sizing:border-box;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;}
    .card-shadow{box-shadow:0 1.6px 3.6px rgba(0,0,0,.132),0 .3px .9px rgba(0,0,0,.108);}
    .task-card{transition:all .2s cubic-bezier(.1,.9,.2,1);border:1px solid #E1E1E1;background:#fff;}
    .task-card:hover{transform:translateY(-1px);box-shadow:0 6.4px 14.4px rgba(0,0,0,.132),0 1.2px 3.6px rgba(0,0,0,.108);}
    .priority-high{border-left:4px solid #D13438;} .priority-medium{border-left:4px solid #FF8C00;} .priority-low{border-left:4px solid #107C10;}
    .column-header-leads{background:linear-gradient(135deg,#0284C7 0%,#0369A1 100%);}      /* Blue */
    .column-header-follow{background:linear-gradient(135deg,#10B981 0%,#059669 100%);}    /* Green */
    .column-header-booked{background:linear-gradient(135deg,#F59E0B 0%,#D97706 100%);}   /* Amber */
    .column-header-done{background:linear-gradient(135deg,#7C3AED 0%,#6D28D9 100%);}     /* Violet */
    .btn-primary{background:#0078D4;border-color:#0078D4;} .btn-primary:hover{background:#106EBE;border-color:#106EBE;}
    .btn-success{background:#107C10;border-color:#107C10;} .btn-success:hover{background:#0B5A0B;border-color:#0B5A0B;}
    .btn-warning{background:#FF8C00;border-color:#FF8C00;} .btn-warning:hover{background:#E6780A;border-color:#E6780A;}
    .btn-purple{background:#5C2D91;border-color:#5C2D91;} .btn-purple:hover{background:#4A1F7A;border-color:#4A1F7A;}
    .input-focus:focus{border-color:#0078D4;box-shadow:0 0 0 1px #0078D4;outline:none;}
    .modal-backdrop{background:rgba(0,0,0,.4);backdrop-filter:blur(2px);}
    .status-dot{width:10px;height:10px;border-radius:9999px;display:inline-block;margin-left:8px;}
    /* Offcanvas fallback: pasti tampil */
    #task-offcanvas{transform:translateX(100%);}
    #task-offcanvas.open{transform:translateX(0)!important;}
    /* Drag & Drop styles */
    .dropzone{min-height:8rem; border:2px dashed transparent; border-radius:.5rem; transition:border-color .15s, background .15s;}
    .dropzone.drag-over{border-color:#60A5FA; background:rgba(59,130,246,.06);}
    .dragging{opacity:.7;}
    .drag-handle{cursor:grab;}
    .drag-handle:active{cursor:grabbing;}
  </style>
</head>
<body class="min-h-full" style="background:linear-gradient(135deg,#F3F2F1 0%,#FAFAFA 100%);">
  <header class="bg-white shadow-sm border-b" style="border-color:#E1E1E1;padding:24px;">
    <main class="flex flex-col lg:flex-row gap-4 lg:gap-0 lg:justify-between lg:items-center">
      <div>
        <h1 class="text-3xl font-semibold" style="color:#323130;">
          CRM Scuba Diving — <span class="font-bold">BALI DIVING</span>
          <span id="save-status" class="align-middle text-xs text-gray-500 ml-2">Idle</span>
          <span id="save-dot" class="status-dot" style="background:#9CA3AF;"></span>
        </h1>
        <p class="mt-2" style="color:#605E5C;">Kelola lead & booking dive dengan mudah</p>
      </div>
      <div class="flex flex-wrap items-center gap-3">
        <div class="relative">
          <input id="search-input" type="text" placeholder="Search lead name/email/phone…" class="input-focus w-64 px-3 py-2 border rounded-md" style="border-color:#E1E1E1;">
          <button onclick="clearSearch()" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">✕</button>
        </div>
        <button onclick="syncNow()" class="btn-primary text-white px-4 py-2 rounded-md flex items-center space-x-2 transition-all font-medium">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
          <span>Sync</span>
        </button>
        <div class="relative">
          <button id="download-btn" onclick="toggleDownloadMenu()" class="btn-purple text-white px-4 py-2 rounded-md flex items-center space-x-2 transition-all font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-4-4m4 4l4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>Export</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
          </button>
          <div id="download-menu" class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border hidden z-10" style="border-color:#E1E1E1;">
            <div class="py-2">
              <button onclick="downloadAs('txt')" class="w-full text-left px-4 py-2 hover:bg-gray-50 flex items-center space-x-2 transition-colors">
                <span class="text-blue-600">📄</span><span style="color:#323130;">Download as TXT</span>
              </button>
              <button onclick="downloadAs('xls')" class="w-full text-left px-4 py-2 hover:bg-gray-50 flex items-center space-x-2 transition-colors">
                <span class="text-green-600">📈</span><span style="color:#323130;">Download as XLS</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </main>
  </header>

  <main class="p-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 max-w-7xl mx-auto">
      <!-- Leads -->
      <section data-column="leads" class="bg-white rounded-lg card-shadow overflow-hidden">
        <div class="column-header-leads text-white p-4">
          <h2 class="text-xl font-semibold">Leads</h2>
          <p class="text-blue-100 text-sm">New inquiries</p>
        </div>
        <div class="p-4">
          <button onclick="showAddModal('leads')" class="w-full btn-primary text-white py-2 px-4 rounded-md mb-4 transition-all font-medium">
            <span class="text-lg mr-2">+</span> Add Lead
          </button>
          <div id="leads-list" class="space-y-3 dropzone"></div>
        </div>
      </section>
      <!-- Follow-Up -->
      <section data-column="follow" class="bg-white rounded-lg card-shadow overflow-hidden">
        <div class="column-header-follow text-white p-4">
          <h2 class="text-xl font-semibold">Follow-Up</h2>
          <p class="text-green-100 text-sm">Need action</p>
        </div>
        <div class="p-4">
          <button onclick="showAddModal('follow')" class="w-full btn-success text-white py-2 px-4 rounded-md mb-4 transition-all font-medium">
            <span class="text-lg mr-2">+</span> Add Lead
          </button>
          <div id="follow-list" class="space-y-3 dropzone"></div>
        </div>
      </section>
      <!-- Booked -->
      <section data-column="booked" class="bg-white rounded-lg card-shadow overflow-hidden">
        <div class="column-header-booked text-white p-4">
          <h2 class="text-xl font-semibold">Booked</h2>
          <p class="text-orange-100 text-sm">Confirmed / Invoice</p>
        </div>
        <div class="p-4">
          <button onclick="showAddModal('booked')" class="w-full btn-warning text-white py-2 px-4 rounded-md mb-4 transition-all font-medium">
            <span class="text-lg mr-2">+</span> Add Lead
          </button>
          <div id="booked-list" class="space-y-3 dropzone"></div>
        </div>
      </section>
      <!-- Completed -->
      <section data-column="completed" class="bg-white rounded-lg card-shadow overflow-hidden">
        <div class="column-header-done text-white p-4">
          <h2 class="text-xl font-semibold">Completed</h2>
          <p class="text-purple-100 text-sm">Finished trips</p>
        </div>
        <div class="p-4">
          <button onclick="showAddModal('completed')" class="w-full btn-purple text-white py-2 px-4 rounded-md mb-4 transition-all font-medium">
            <span class="text-lg mr-2">+</span> Add Lead
          </button>
          <div id="completed-list" class="space-y-3 dropzone"></div>
        </div>
      </section>
    </div>
  </main>

  <!-- Add Lead Modal -->
  <div id="lead-modal" class="fixed inset-0 modal-backdrop hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-2xl mx-4 card-shadow">
      <h3 class="text-xl font-semibold mb-4" style="color:#323130;">Add Lead — BALI DIVING</h3>
      <form id="lead-form" onsubmit="handleAddLead(event)">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-2" style="color:#323130;">Lead Name</label>
            <input id="f-name" type="text" required class="input-focus w-full px-3 py-2 border rounded-md" style="border-color:#E1E1E1;">
          </div>
          <div>
            <label class="block text-sm font-medium mb-2" style="color:#323130;">Email</label>
            <input id="f-email" type="email" class="input-focus w-full px-3 py-2 border rounded-md" style="border-color:#E1E1E1;">
          </div>
          <div>
            <label class="block text-sm font-medium mb-2" style="color:#323130;">Phone / WhatsApp</label>
            <input id="f-phone" type="text" class="input-focus w-full px-3 py-2 border rounded-md" style="border-color:#E1E1E1;">
          </div>
          <div>
            <label class="block text-sm font-medium mb-2" style="color:#323130;">Country</label>
            <input id="f-country" type="text" placeholder="e.g. Singapore" class="input-focus w-full px-3 py-2 border rounded-md" style="border-color:#E1E1E1;">
          </div>
          <div>
            <label class="block text-sm font-medium mb-2" style="color:#323130;">Source</label>
            <select id="f-source" class="input-focus w-full px-3 py-2 border rounded-md" style="border-color:#E1E1E1;">
              <option value="Website">Website</option>
              <option value="Instagram">Instagram</option>
              <option value="Facebook">Facebook</option>
              <option value="Referral">Referral</option>
              <option value="Walk-in">Walk-in</option>
              <option value="Other" selected>Other</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium mb-2" style="color:#323130;">Package</label>
            <select id="f-package" class="input-focus w-full px-3 py-2 border rounded-md" style="border-color:#E1E1E1;">
              <option value="Fun Dive">Fun Dive</option>
              <option value="Discover Scuba">Discover Scuba</option>
              <option value="Open Water">Open Water</option>
              <option value="Advanced">Advanced</option>
              <option value="Rescue">Rescue</option>
              <option value="Other" selected>Other</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium mb-2" style="color:#323130;">Cert Level</label>
            <select id="f-cert" class="input-focus w-full px-3 py-2 border rounded-md" style="border-color:#E1E1E1;">
              <option value="None">None</option>
              <option value="OW">OW</option>
              <option value="AOW">AOW</option>
              <option value="Rescue">Rescue</option>
              <option value="DM">DM</option>
              <option value="Instructor">Instructor</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium mb-2" style="color:#323130;">Dive Date</label>
            <input id="f-date" type="date" class="input-focus w-full px-3 py-2 border rounded-md" style="border-color:#E1E1E1;">
          </div>
          <div>
            <label class="block text-sm font-medium mb-2" style="color:#323130;">Pax</label>
            <input id="f-pax" type="number" min="1" step="1" value="1" class="input-focus w-full px-3 py-2 border rounded-md" style="border-color:#E1E1E1;">
          </div>
          <div>
            <label class="block text-sm font-medium mb-2" style="color:#323130;">Budget (USD)</label>
            <input id="f-budget" type="number" step="0.01" class="input-focus w-full px-3 py-2 border rounded-md" style="border-color:#E1E1E1;">
          </div>
          <div>
            <label class="block text-sm font-medium mb-2" style="color:#323130;">Priority</label>
            <select id="f-priority" class="input-focus w-full px-3 py-2 border rounded-md" style="border-color:#E1E1E1;">
              <option value="low">Low</option>
              <option value="medium" selected>Medium</option>
              <option value="high">High</option>
            </select>
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm font-medium mb-2" style="color:#323130;">Notes</label>
            <textarea id="f-notes" rows="3" class="input-focus w-full px-3 py-2 border rounded-md" style="border-color:#E1E1E1;"></textarea>
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm font-medium mb-2" style="color:#323130;">URL Link</label>
            <input id="f-url" type="url" placeholder="https://…" class="input-focus w-full px-3 py-2 border rounded-md" style="border-color:#E1E1E1;">
          </div>
        </div>
        <div class="flex justify-end space-x-3 mt-6">
          <button type="button" onclick="hideAddModal()" class="px-4 py-2 font-medium" style="color:#605E5C;">Cancel</button>
          <button id="btn-add" type="submit" class="btn-primary px-6 py-2 text-white rounded-md font-medium">Add Lead</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Offcanvas: Lead Details -->
  <div id="task-offcanvas" class="fixed inset-y-0 right-0 w-[28rem] bg-white card-shadow transform transition-transform duration-300 ease-in-out z-50">
    <div class="h-full flex flex-col">
      <div class="column-header-leads text-white p-6">
        <div class="flex justify-between items-center">
          <h3 class="text-xl font-semibold">Lead Details — BALI DIVING</h3>
          <button onclick="closeOffcanvas()" class="text-white hover:text-gray-200 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
      </div>
      <div class="flex-1 overflow-y-auto p-6">
        <form id="edit-form" onsubmit="handleUpdate(event)">
          <div class="grid grid-cols-1 gap-4">
            <div>
              <label class="block text-sm font-medium mb-2" style="color:#323130;">Lead Name</label>
              <input id="e-name" type="text" required class="input-focus w-full px-3 py-2 border rounded-md" style="border-color:#E1E1E1;">
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium mb-2" style="color:#323130;">Email</label>
                <input id="e-email" type="email" class="input-focus w-full px-3 py-2 border rounded-md" style="border-color:#E1E1E1;">
              </div>
              <div>
                <label class="block text-sm font-medium mb-2" style="color:#323130;">Phone/WA</label>
                <input id="e-phone" type="text" class="input-focus w-full px-3 py-2 border rounded-md" style="border-color:#E1E1E1;">
              </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium mb-2" style="color:#323130;">Country</label>
                <input id="e-country" type="text" class="input-focus w-full px-3 py-2 border rounded-md" style="border-color:#E1E1E1;">
              </div>
              <div>
                <label class="block text-sm font-medium mb-2" style="color:#323130;">Source</label>
                <select id="e-source" class="input-focus w-full px-3 py-2 border rounded-md" style="border-color:#E1E1E1;">
                  <option>Website</option><option>Instagram</option><option>Facebook</option><option>Referral</option><option>Walk-in</option><option selected>Other</option>
                </select>
              </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium mb-2" style="color:#323130;">Package</label>
                <select id="e-package" class="input-focus w-full px-3 py-2 border rounded-md" style="border-color:#E1E1E1;">
                  <option>Fun Dive</option><option>Discover Scuba</option><option>Open Water</option><option>Advanced</option><option>Rescue</option><option selected>Other</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium mb-2" style="color:#323130;">Cert Level</label>
                <select id="e-cert" class="input-focus w-full px-3 py-2 border rounded-md" style="border-color:#E1E1E1;">
                  <option>None</option><option>OW</option><option>AOW</option><option>Rescue</option><option>DM</option><option>Instructor</option>
                </select>
              </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium mb-2" style="color:#323130;">Dive Date</label>
                <input id="e-date" type="date" class="input-focus w-full px-3 py-2 border rounded-md" style="border-color:#E1E1E1;">
              </div>
              <div>
                <label class="block text-sm font-medium mb-2" style="color:#323130;">Pax</label>
                <input id="e-pax" type="number" min="1" step="1" class="input-focus w-full px-3 py-2 border rounded-md" style="border-color:#E1E1E1;">
              </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium mb-2" style="color:#323130;">Budget (USD)</label>
                <input id="e-budget" type="number" step="0.01" class="input-focus w-full px-3 py-2 border rounded-md" style="border-color:#E1E1E1;">
              </div>
              <div>
                <label class="block text-sm font-medium mb-2" style="color:#323130;">Priority</label>
                <select id="e-priority" class="input-focus w-full px-3 py-2 border rounded-md" style="border-color:#E1E1E1;">
                  <option value="low">Low</option><option value="medium">Medium</option><option value="high">High</option>
                </select>
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium mb-2" style="color:#323130;">Assigned To</label>
              <input id="e-assigned" type="text" placeholder="Staff name" class="input-focus w-full px-3 py-2 border rounded-md" style="border-color:#E1E1E1;">
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium mb-2" style="color:#323130;">Next Action Date</label>
                <input id="e-nextdate" type="date" class="input-focus w-full px-3 py-2 border rounded-md" style="border-color:#E1E1E1;">
              </div>
              <div>
                <label class="block text-sm font-medium mb-2" style="color:#323130;">Next Action</label>
                <input id="e-next" type="text" placeholder="Call/Invoice/Email…" class="input-focus w-full px-3 py-2 border rounded-md" style="border-color:#E1E1E1;">
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium mb-2" style="color:#323130;">URL Link</label>
              <div class="flex gap-2">
                <input id="e-url" type="url" class="input-focus flex-1 px-3 py-2 border rounded-md" style="border-color:#E1E1E1;">
                <button type="button" onclick="openLeadUrl()" class="btn-primary px-3 py-2 text-white rounded-md" title="Open URL">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </button>
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium mb-2" style="color:#323130;">Notes</label>
              <textarea id="e-notes" rows="3" class="input-focus w-full px-3 py-2 border rounded-md" style="border-color:#E1E1E1;"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium mb-2" style="color:#323130;">Move to Column</label>
                <select id="e-column" class="input-focus w-full px-3 py-2 border rounded-md" style="border-color:#E1E1E1;">
                  <option value="leads">Leads</option>
                  <option value="follow">Follow-Up</option>
                  <option value="booked">Booked</option>
                  <option value="completed">Completed</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium mb-2" style="color:#323130;">Stage</label>
                <select id="e-stage" class="input-focus w-full px-3 py-2 border rounded-md" style="border-color:#E1E1E1;">
                  <option>New</option><option>Contacted</option><option>Negotiation</option><option>Confirmed</option><option>Finished</option><option>Lost</option>
                </select>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium mb-2" style="color:#323130;">Brand</label>
              <input type="text" value="BALI DIVING" disabled class="w-full px-3 py-2 border rounded-md bg-gray-100 text-gray-600" style="border-color:#E1E1E1;">
            </div>

            <div>
              <label class="block text-sm font-medium mb-2" style="color:#323130;">Created</label>
              <div id="e-created" class="px-3 py-2 border rounded-md" style="background:#F3F2F1;border-color:#E1E1E1;color:#605E5C;"></div>
            </div>
          </div>

          <div class="flex gap-3 mt-6">
            <button id="btn-update" type="submit" class="flex-1 btn-primary text-white py-2 px-4 rounded-md font-medium">Update</button>
            <button type="button" onclick="confirmDeleteLead()" class="text-white py-2 px-4 rounded-md font-medium" style="background:#D13438;">Delete</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div id="offcanvas-backdrop" class="fixed inset-0 modal-backdrop hidden z-40" onclick="closeOffcanvas()"></div>

  <script>
    // ================= Config =================
    const SAVE_ENDPOINT = 'save_data.php?file=data.txt';
    const BRAND_FIXED = 'BALI DIVING';

    // ================ State ================
    let leads = [];
    let currentColumn = 'leads';
    let isLoading = false;
    let saveTimer = null;
    let currentEditing = null;
    let isDragging = false;  // untuk cegah klik saat drag
    let dragId = null;

    // ============== Helpers ================
    const $ = (sel, root=document)=>root.querySelector(sel);
    function ci(s){return (s||'').toString().toLowerCase();}
    function escapeHtml(t){const d=document.createElement('div');d.textContent=t;return d.innerHTML;}
    function setSaveStatus(txt,color){ const s=$("#save-status"),d=$("#save-dot"); if(s)s.textContent=txt; if(d)d.style.background=color||"#9CA3AF"; }
    function toast(msg,type){
      const div=document.createElement('div');
      div.className=`fixed top-4 right-4 px-4 py-2 rounded-lg text-white z-50 font-medium ${type==='success'?'bg-green-500':'bg-red-500'}`;
      div.textContent=msg; document.body.appendChild(div); setTimeout(()=>div.remove(),2500);
    }
    function normUrl(u){ if(!u) return ''; let x=String(u).trim(); if(!/^https?:\/\//i.test(x)) x='https://'+x; return x; }

    // ============== Storage & Sync =========
    function persistLocal(){ localStorage.setItem('crm-bali-diving', JSON.stringify(leads)); scheduleAutosave(); }
    function loadLocal(){ const s=localStorage.getItem('crm-bali-diving'); if(s){ try{ leads=JSON.parse(s)||[]; }catch{} } }
    function scheduleAutosave(){ clearTimeout(saveTimer); saveTimer=setTimeout(()=>saveToServer(),600); }
    async function saveToServer(showToast=false){
      try{
        setSaveStatus('Saving…','#F59E0B');
        const res = await fetch(SAVE_ENDPOINT,{ method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(leads), credentials:'include' });
        if(!res.ok) throw new Error('HTTP '+res.status);
        await res.json().catch(()=>({ok:true}));
        setSaveStatus('Saved ✔','#10B981');
        if(showToast) toast('Synced to server','success');
      }catch(e){
        console.error(e);
        setSaveStatus('Save error','#EF4444');
        toast('Save error. Cek save_data.php & permission.','error');
      }
    }
    function syncNow(){ saveToServer(true); }

    // ============== Search ==============
    $("#search-input")?.addEventListener('input', ()=>renderAll());
    function clearSearch(){ const i=$("#search-input"); if(i){ i.value=''; renderAll(); } }

    // ============== Rendering ==============
    function renderAll(){
      renderColumn('leads',     $('#leads-list'));
      renderColumn('follow',    $('#follow-list'));
      renderColumn('booked',    $('#booked-list'));
      renderColumn('completed', $('#completed-list'));
      attachDropzones(); // pastikan dropzone listener aktif lagi setelah re-render
    }
    function renderColumn(col, container){
      const q = ci($('#search-input')?.value||'');
      const items = leads.filter(l => l.column===col).filter(l=>{
        if(!q) return true;
        return [l.name,l.email,l.phone,l.country,l.source,l.package,l.cert].some(v=>ci(v).includes(q));
      });
      container.innerHTML='';
      items.forEach(item=>container.appendChild(card(item)));
    }

    function chip(text, bg, fg){ return `<span class="text-xs px-2 py-1 rounded-full" style="background:${bg};color:${fg};">${escapeHtml(text||'')}</span>`; }

    function card(item){
      const el=document.createElement('div');
      el.className=`task-card rounded-lg p-3 card-shadow priority-${item.priority||'medium'} cursor-pointer`;
      el.dataset.id=item.id;

      // === Drag capability ===
      el.setAttribute('draggable','true');
      el.addEventListener('dragstart', (e)=>{
        isDragging = true;
        dragId = item.id;
        el.classList.add('dragging');
        e.dataTransfer.setData('text/plain', item.id);
        e.dataTransfer.effectAllowed = 'move';
      });
      el.addEventListener('dragend', ()=>{
        isDragging = false;
        dragId = null;
        el.classList.remove('dragging');
      });

      const title=escapeHtml(item.name||'');
      const notes =escapeHtml(item.notes||'');
      const dateStr = item.dive_date ? new Date(item.dive_date).toLocaleDateString() : '';

      el.innerHTML = `
        <div class="flex justify-between items-start mb-2">
          <div class="flex-1">
            <div class="flex items-center gap-2">
              <span class="drag-handle inline-flex items-center justify-center w-5 h-5 rounded border border-gray-300 text-xs" title="Drag">⋮⋮</span>
              <h4 class="font-medium" style="color:#323130;">${title}</h4>
            </div>
            <div class="text-xs text-gray-500 mt-1">${item.email||''}${item.email&&item.phone?' · ':''}${item.phone||''}</div>
          </div>
          <button onclick="event.stopPropagation(); deleteLead('${item.id}')" class="ml-2 text-gray-400 hover:text-red-500" title="Delete">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        ${notes?`<p class="text-sm mb-2" style="color:#605E5C;">${notes}</p>`:''}
        <div class="flex flex-wrap gap-2 mb-2">
          ${item.package?chip(item.package,'#E1F5FE','#0277BD'):''}
          ${item.cert?chip(item.cert,'#F3E5F5','#7B1FA2'):''}
          ${item.source?chip(item.source,'#FFF7ED','#B45309'):''}
          ${dateStr?chip('Dive: '+dateStr,'#ECFDF5','#065F46'):''}
        </div>
        <div class="flex items-center gap-2">
          ${item.url?`
            <button type="button" class="text-xs px-2 py-1 rounded border border-gray-300 hover:bg-gray-50 inline-flex items-center gap-1"
                    data-action="open-url" title="Open link">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
              </svg>
              Open
            </button>`:''}
        </div>
      `;

      // klik untuk open offcanvas (kecuali sedang drag)
      el.addEventListener('click', ()=>{ if(!isDragging) openOffcanvas(item); });
      const btn=el.querySelector('[data-action="open-url"]');
      if(btn){ btn.addEventListener('click', e=>{ e.stopPropagation(); window.open(normUrl(item.url),'_blank','noopener,noreferrer'); }); }
      return el;
    }

    // ============== Drag & Drop (antara board) ==============
    function attachDropzones(){
      document.querySelectorAll('.dropzone').forEach(dz=>{
        dz.addEventListener('dragover', ev=>{
          ev.preventDefault();
          dz.classList.add('drag-over');
          ev.dataTransfer.dropEffect = 'move';
        });
        dz.addEventListener('dragleave', ()=>dz.classList.remove('drag-over'));
        dz.addEventListener('drop', ev=>{
          ev.preventDefault();
          dz.classList.remove('drag-over');
          const id = ev.dataTransfer.getData('text/plain') || dragId;
          if(!id) return;
          // Tentukan target column dari parent section
          const section = dz.closest('section[data-column]');
          const targetCol = section ? section.getAttribute('data-column') : null;
          if(!targetCol) return;

          const idx = leads.findIndex(l=>l.id===id);
          if(idx===-1) return;

          // Update column
          leads[idx] = { ...leads[idx], column: targetCol };
          persistLocal();
          renderAll();
        });
      });
    }

    // ============== Modal Add ==============
    function showAddModal(col){ currentColumn=col; const m=$("#lead-modal"); m.classList.remove('hidden'); m.classList.add('flex'); $("#f-name").focus(); }
    function hideAddModal(){ const m=$("#lead-modal"); m.classList.add('hidden'); m.classList.remove('flex'); $("#lead-form").reset(); }

    async function handleAddLead(e){
      e.preventDefault(); if(isLoading) return;
      const name = $("#f-name").value.trim(); if(!name) return;
      isLoading=true; const b=$("#btn-add"); b.textContent='Adding...'; b.disabled=true;

      const obj = {
        id: Date.now().toString(),
        column: currentColumn,      // leads | follow | booked | completed
        stage: 'New',
        name,
        email:   $("#f-email").value.trim(),
        phone:   $("#f-phone").value.trim(),
        country: $("#f-country").value.trim(),
        source:  $("#f-source").value,
        package: $("#f-package").value,
        cert:    $("#f-cert").value,
        dive_date: $("#f-date").value || '',
        pax:     Number($("#f-pax").value||1),
        budget:  Number($("#f-budget").value||0),
        priority: $("#f-priority").value,
        assigned_to: '',
        next_action_date: '',
        next_action: '',
        url:     $("#f-url").value.trim(),
        notes:   $("#f-notes").value.trim(),
        brand:   BRAND_FIXED,
        created_at: new Date().toISOString()
      };

      try{
        leads.push(obj);
        persistLocal(); renderAll(); hideAddModal(); toast('Lead added!','success');
      }finally{ isLoading=false; b.textContent='Add Lead'; b.disabled=false; }
    }

    // ============== Offcanvas ==============
    function openOffcanvas(item){
      currentEditing = item;
      $("#e-name").value = item.name||'';
      $("#e-email").value= item.email||'';
      $("#e-phone").value= item.phone||'';
      $("#e-country").value=item.country||'';
      $("#e-source").value=item.source||'Other';
      $("#e-package").value=item.package||'Other';
      $("#e-cert").value=item.cert||'None';
      $("#e-date").value=item.dive_date||'';
      $("#e-pax").value=item.pax||1;
      $("#e-budget").value=item.budget||'';
      $("#e-priority").value=item.priority||'medium';
      $("#e-assigned").value=item.assigned_to||'';
      $("#e-nextdate").value=item.next_action_date||'';
      $("#e-next").value=item.next_action||'';
      $("#e-url").value=item.url||'';
      $("#e-notes").value=item.notes||'';
      $("#e-column").value=item.column||'leads';
      $("#e-stage").value=item.stage||'New';
      $("#e-created").textContent = new Date(item.created_at).toLocaleString();
      $("#offcanvas-backdrop").classList.remove('hidden');
      $("#task-offcanvas").classList.add('open');
      document.body.style.overflow='hidden';
    }
    function closeOffcanvas(){ $("#task-offcanvas").classList.remove('open'); $("#offcanvas-backdrop").classList.add('hidden'); document.body.style.overflow='auto'; currentEditing=null; }

    async function handleUpdate(e){
      e.preventDefault(); if(!currentEditing||isLoading) return;
      const idx = leads.findIndex(x=>x.id===currentEditing.id); if(idx===-1) return;
      isLoading=true; const ub=$("#btn-update"); ub.textContent='Updating...'; ub.disabled=true;

      const updated = {
        ...currentEditing,
        name: $("#e-name").value.trim(),
        email: $("#e-email").value.trim(),
        phone: $("#e-phone").value.trim(),
        country: $("#e-country").value.trim(),
        source: $("#e-source").value,
        package: $("#e-package").value,
        cert: $("#e-cert").value,
        dive_date: $("#e-date").value || '',
        pax: Number($("#e-pax").value||1),
        budget: Number($("#e-budget").value||0),
        priority: $("#e-priority").value,
        assigned_to: $("#e-assigned").value.trim(),
        next_action_date: $("#e-nextdate").value || '',
        next_action: $("#e-next").value.trim(),
        url: $("#e-url").value.trim(),
        notes: $("#e-notes").value.trim(),
        column: $("#e-column").value,
        stage: $("#e-stage").value
      };

      try{
        leads[idx]=updated; persistLocal(); renderAll(); closeOffcanvas(); toast('Updated!','success');
      }finally{ isLoading=false; ub.textContent='Update'; ub.disabled=false; }
    }

    function confirmDeleteLead(){
      if(!currentEditing) return;
      if(!confirm('Delete this lead?')) return;
      deleteLead(currentEditing.id); closeOffcanvas();
    }

    function deleteLead(id){
      const i=leads.findIndex(x=>x.id===id); if(i===-1) return;
      leads.splice(i,1); persistLocal(); renderAll(); toast('Lead deleted.','success');
    }

    function openLeadUrl(){
      const u = $("#e-url").value.trim();
      if(!u){ toast('No URL to open','error'); return; }
      window.open(normUrl(u),'_blank','noopener,noreferrer');
    }

    // ============== Export ==============
    function toggleDownloadMenu(){ document.getElementById('download-menu').classList.toggle('hidden'); }
    document.addEventListener('click',function(e){
      const btn=document.getElementById('download-btn'); const menu=document.getElementById('download-menu');
      if(btn && menu && !btn.contains(e.target) && !menu.contains(e.target)) menu.classList.add('hidden');
    });

    function downloadAs(format){
      document.getElementById('download-menu').classList.add('hidden');
      if(leads.length===0){ toast("No data","error"); return; }
      switch(format){
        case 'txt': return downloadTXT();
        case 'xls': return downloadXLS();
      }
    }

    function downloadTXT(){
      const blob=new Blob([JSON.stringify(leads,null,2)],{type:'text/plain'});
      const url=URL.createObjectURL(blob);
      const a=document.createElement('a'); a.href=url; a.download='bali-diving-crm.txt'; a.click(); URL.revokeObjectURL(url);
      toast("TXT downloaded!","success");
    }

    function sortForExport(arr){
      // Sort by Column → DiveDate → Source
      return [...arr].sort((a,b)=>{
        const c1 = ci(a.column).localeCompare(ci(b.column));
        if(c1!==0) return c1;
        const aDate = a.dive_date||'';
        const bDate = b.dive_date||'';
        const c2 = ci(aDate).localeCompare(ci(bDate));
        if(c2!==0) return c2;
        return ci(a.source).localeCompare(ci(b.source));
      });
    }

    function downloadXLS(){
      const headers = ['Column','Stage','LeadName','Email','Phone','Country','Source','Package','CertLevel','DiveDate','Pax','Budget','Priority','AssignedTo','NextActionDate','NextAction','Notes','URL','Brand'];
      const rows = sortForExport(leads).map(l=>[
        l.column||'', l.stage||'', l.name||'', l.email||'', l.phone||'', l.country||'',
        l.source||'', l.package||'', l.cert||'', l.dive_date||'', l.pax||'',
        (l.budget??'').toString(), l.priority||'', l.assigned_to||'', l.next_action_date||'', l.next_action||'',
        l.notes||'', l.url||'', l.brand||BRAND_FIXED
      ]);

      const escapeCell = s => escapeHtml(String(s));
      const tableHTML = `
        <table>
          <thead><tr>${headers.map(h=>`<th>${escapeCell(h)}</th>`).join('')}</tr></thead>
          <tbody>
            ${rows.map(r=>`<tr>${r.map(c=>`<td>${escapeCell(c)}</td>`).join('')}</tr>`).join('')}
          </tbody>
        </table>
      `;

      const blob = new Blob([tableHTML], { type:'application/vnd.ms-excel' });
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url; a.download = 'bali-diving-crm.xls'; a.click();
      URL.revokeObjectURL(url);
      toast("XLS downloaded!","success");
    }

    // ============== Seed & Bootstrap ==============
    function seed(){
      leads = [
        {id:'ld-1', column:'leads', stage:'New', name:'John Diver', email:'john@example.com', phone:'+62 812-xxx', country:'Singapore',
         source:'Website', package:'Open Water', cert:'None', dive_date:'', pax:2, budget:600, priority:'medium',
         assigned_to:'', next_action_date:'', next_action:'', url:'', notes:'Asking for beginner course', brand:BRAND_FIXED, created_at:new Date().toISOString()},
        {id:'ld-2', column:'follow', stage:'Contacted', name:'Mika Chen', email:'mika@example.com', phone:'+65 9xx', country:'Singapore',
         source:'Instagram', package:'Fun Dive', cert:'AOW', dive_date:'2025-12-20', pax:3, budget:900, priority:'high',
         assigned_to:'Ayu', next_action_date:'2025-11-01', next_action:'Send quotation', url:'', notes:'Wants Nusa Penida', brand:BRAND_FIXED, created_at:new Date().toISOString()},
        {id:'ld-3', column:'booked', stage:'Confirmed', name:'Kenta Sato', email:'kenta@example.jp', phone:'+81 80-xxx', country:'Japan',
         source:'Referral', package:'Advanced', cert:'OW', dive_date:'2025-11-15', pax:1, budget:450, priority:'medium',
         assigned_to:'Budi', next_action_date:'2025-11-10', next_action:'Collect deposit', url:'', notes:'Prefers morning trip', brand:BRAND_FIXED, created_at:new Date().toISOString()}
      ];
    }

    document.addEventListener('DOMContentLoaded', async ()=>{
      loadLocal();
      if(leads.length===0) seed();
      renderAll();
      saveToServer(); // initial save
      // modal backdrop close
      document.getElementById('lead-modal').addEventListener('click',e=>{ if(e.target===e.currentTarget) hideAddModal(); });
      // ESC offcanvas
      document.addEventListener('keydown',e=>{ if(e.key==='Escape') closeOffcanvas(); });
    });
  </script>
</body>
</html>
