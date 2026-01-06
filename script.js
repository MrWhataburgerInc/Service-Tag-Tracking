// script.js

// Define different checklists based on device type
const deviceChecklists = {
  'chromebook': [
    { type: 'text', content: 'Chromebook Checklist:' },
    { type: 'text', content: '1. Grab device from QA desk, Take 1 device at a time' },
    { type: 'checkbox', content: 'a) Compare Service Tag on device to the Service Tag on paper Diagnostics Report, and ST tag in Field Power - all should match' },
    { type: 'checkbox', content: 'b) Check Diag. Report paper to ensure everything has been filled out' },
    { type: 'text', content: '2. Take device and inspect the outside of device for physical damage' },
    { type: 'checkbox', content: 'a) Check bottom cover screws are screwed in' },
    { type: 'checkbox', content: 'b) Service tag and school stickers are applied to the bottom cover' },
    { type: 'checkbox', content: 'c) Push slightly on the hinges to check for separation of the top cover' },
    { type: 'checkbox', content: 'd) Check the internals of ports to ensure there is no physical debris in them' },
    { type: 'checkbox', content: 'e) Check outside of device for cracks or chips' },
    { type: 'checkbox', content: 'f) Open screen and check LCD, bezel, keyboard, and palmrest for cracks or other damage/not snapped together' },
    { type: 'checkbox', content: 'g) Plug in device power, check for charging light. Power on device.' },
    { type: 'checkbox', content: 'h) Check all ports for functionality (USB, audio)' },
    { type: 'checkbox', content: 'i) Make sure whiteburn wasn’t missed on LCD' },
    { type: 'checkbox', content: 'j) Check for touch screen capability if applicable (Bottom left, Bottom right, then Middle of screen).' },
    { type: 'checkbox', content: 'k) Switch to keyboard and check all keys are functional, test mouse/trackpad' },
    { type: 'text', content: '*FOR CHROMEBOOKS*' },
    { type: 'checkbox', content: 'l) Pull up Chromebook Diagnostics and check battery health' },
    { type: 'checkbox', content: 'm) Close diag tab and pull up internal Service Tag and OS version, Ensure Service Tag matches with paper and bottom cover sticker. Check OS version is relatively up to date' },
    { type: 'checkbox', content: 'n) Check bezel magnet is correctly placed' },
    { type: 'text', content: '*Clean ALL devices when physical QA ends!*' },
    { type: 'checkbox', content: 'o) Check WHOLE device for cleanliness, Not just screen!' },
    { type: 'text', content: '3. Once Physical device is checked, pull up the job in Field Power' },
    { type: 'checkbox', content: 'a) Check "Technician Remarks" is filled out completely and correctly' },
    { type: 'checkbox', content: 'b) Check for Attachments if needed' },
    { type: 'checkbox', content: 'c) Check Forms - Repair Checklist v2.0 is filled out completely' },
    { type: 'checkbox', content: 'd) Check "Part Request" has all parts, Parts are for the correct device and ADP or NON-ADP and that all parts are "Approved" and "Allocated"' },
    { type: 'checkbox', content: 'e) Copy all of the "Technician Remarks". Click on the green button at the top labeled "Completed”. Paste Tech Remarks in the "Remarks" field.' },
    { type: 'checkbox', content: 'f) Click on the drop-down menu under "Reason". Select the checkbox for "Completed -No Issues". Click the check box on top right of field.' },
    { type: 'checkbox', content: 'g) A new box that is labeled "Submit" will appear on the top right of screen. Click box and a warning box will pop up asking if everything is correct. ENSURE EVERYTHING IS 100% CORRECT.' },
    { type: 'text', content: '4. After you click yes and submit the job, YOU CAN NOT EDIT IT AT ALL. THIS WILL CLOSE THE JOB, YOU CANNOT UNDO THIS.' },
    { type: 'text', content: 'SIGN YOUR NAME AND DATE ON THE SHEET OF PAPER!!!' },
    { type: 'checkbox', content: ' NAME AND DATE SIGNED' },
  ],
  'dell-latitude': [
    { type: 'text', content: 'Dell Latitude Checklist:' },
    { type: 'text', content: '1. Grab device from QA desk, Take 1 device at a time' },
    { type: 'checkbox', content: 'a) Compare Service Tag on device to the Service Tag on paper Diagnostics Report, and ST tag in Field Power - all should match' },
    { type: 'checkbox', content: 'b) Check Diag. Report paper to ensure everything has been filled out' },
    { type: 'text', content: '2. Take device and inspect the outside of device for physical damage' },
    { type: 'checkbox', content: 'a) Check bottom cover screws are screwed in' },
    { type: 'checkbox', content: 'b) Service tag and school stickers are applied to the bottom cover' },
    { type: 'checkbox', content: 'c) Push slightly on the hinges to check for separation of the top cover' },
    { type: 'checkbox', content: 'd) Check the internals of ports to ensure there is no physical debris in them' },
    { type: 'checkbox', content: 'e) Check outside of device for cracks or chips' },
    { type: 'checkbox', content: 'f) Open screen and check LCD, bezel, keyboard, and palmrest for cracks or other damage/not snapped together' },
    { type: 'checkbox', content: 'g) Plug in device power, check for charging light. Power on device.' },
    { type: 'checkbox', content: 'h) Check all ports for functionality (USB, audio)' },
    { type: 'checkbox', content: 'i) Make sure whiteburn wasn’t missed on LCD' },
    { type: 'checkbox', content: 'j) Check for touch screen capability if applicable (Bottom left, Bottom right, then Middle of screen).' },
    { type: 'checkbox', content: 'k) Switch to keyboard and check all keys are functional, test mouse/trackpad' },
    { type: 'text', content: '*Clean ALL devices when physical QA ends!*' },
    { type: 'checkbox', content: 'o) Check WHOLE device for cleanliness, Not just screen!' },
    { type: 'text', content: '3. Once Physical device is checked, pull up the job in Field Power' },
    { type: 'checkbox', content: 'a) Check "Technician Remarks" is filled out completely and correctly' },
    { type: 'checkbox', content: 'b) Check for Attachments if needed' },
    { type: 'checkbox', content: 'c) Check Forms - Repair Checklist v2.0 is filled out completely' },
    { type: 'checkbox', content: 'd) Check "Part Request" has all parts, Parts are for the correct device and ADP or NON-ADP and that all parts are "Approved" and "Allocated"' },
    { type: 'checkbox', content: 'e) Copy all of the "Technician Remarks". Click on the green button at the top labeled "Completed”. Paste Tech Remarks in the "Remarks" field.' },
    { type: 'checkbox', content: 'f) Click on the drop-down menu under "Reason". Select the checkbox for "Completed -No Issues". Click the check box on top right of field.' },
    { type: 'checkbox', content: 'g) A new box that is labeled "Submit" will appear on the top right of screen. Click box and a warning box will pop up asking if everything is correct. ENSURE EVERYTHING IS 100% CORRECT.' },
    { type: 'text', content: '4. After you click yes and submit the job, YOU CAN NOT EDIT IT AT ALL. THIS WILL CLOSE THE JOB, YOU CANNOT UNDO THIS.' },
    { type: 'text', content: 'SIGN YOUR NAME AND DATE ON THE SHEET OF PAPER!!!' },
    { type: 'checkbox', content: ' NAME AND DATE SIGNED' },
  ],
  // Add more device types as needed
  'hp-probook': [
    { type: 'text', content: 'HP Probook Checklist:' },
    { type: 'checkbox', content: '1. Boot to HP Diagnostics' },
    { type: 'checkbox', content: '2. Check hard drive health' },
    { type: 'checkbox', content: '3. Verify webcam functionality' },
    { type: 'checkbox', content: '4. Test HDMI/DisplayPort output' },
    { type: 'checkbox', content: '5. Update HP Support Assistant' },
    { type: 'checkbox', content: '6. Check for physical damage on hinges' },
    { type: 'text', content: '7. Ensure all updates are complete' },
  ]
};

let currentTag = null;
let currentDeviceType = 'chromebook'; // Default device type

const checklistEl = document.getElementById("checklist");
const progressBar = document.getElementById("progress-bar");
const qaCompleteMsg = document.getElementById("qa-complete");
const repairBtn = document.getElementById("reset-btn"); // Renamed from repairBtn to completeBtn for clarity
const kickbackBtn = document.getElementById("kickback-btn");
const deviceSelector = document.getElementById("device-selector");
const deviceTypeTabsContainer = document.getElementById("device-type-tabs"); // New element for tabs
const checkAllBtn = document.getElementById("check-all-btn"); // New button for check all
const passedCounterEl = document.getElementById("passed-counter"); // New element for passed count
const kickbackCounterEl = document.getElementById("kickback-counter"); // New element for kickback count

// Add event listeners for new buttons (will be added in HTML soon)
checkAllBtn.addEventListener('click', checkAllChecklistItems);
repairBtn.addEventListener('click', resetChecklist); // Now refers to complete
kickbackBtn.addEventListener('click', kickbackDevice);

// --- Functions for device type tabs ---
function createDeviceTypeTabs() {
  deviceTypeTabsContainer.innerHTML = ''; // Clear existing tabs
  for (const type in deviceChecklists) {
    const tabButton = document.createElement('button');
    tabButton.textContent = type.replace(/-/g, ' ').toUpperCase(); // Format for display
    tabButton.classList.add('tab-button');
    if (type === currentDeviceType) {
      tabButton.classList.add('active');
    }
    tabButton.dataset.deviceType = type;
    tabButton.onclick = () => switchDeviceType(type);
    deviceTypeTabsContainer.appendChild(tabButton);
  }
}

function switchDeviceType(type) {
  currentDeviceType = type;
  // Update active tab styling
  document.querySelectorAll('.tab-button').forEach(btn => {
    btn.classList.remove('active');
  });
  document.querySelector(`.tab-button[data-device-type="${type}"]`).classList.add('active');

  // Clear current device selection if it's not applicable to the new type
  // Or, ideally, devices persist across types, but for simplicity, let's clear
  // If you want devices to persist across types, we need a more complex state
  // This will clear the current checklist, which is generally what you want when switching types
  currentTag = null;
  checklistEl.innerHTML = '';
  progressBar.style.width = '0%';
  progressBar.textContent = '0%';
  qaCompleteMsg.style.display = 'none';
  repairBtn.style.display = 'none';
  checkAllBtn.style.display = 'none'; // Hide check all if no device is selected
  document.getElementById("serviceTagInput").value = ''; // Clear input

  // Re-render device buttons, potentially filtering by type if needed
  // For now, all buttons show, but the *selected* device's checklist will match the active type
  updateAllDeviceButtons();
}

// --- Modified loadDevice function to incorporate device type ---
function loadDevice() {
  const tag = document.getElementById("serviceTagInput").value.trim();
  if (!tag) return;

  // Check if button already exists. If not, create it
  if (!document.getElementById(`btn-${tag}`)) {
    createDeviceButton(tag);
  } else {
    // If button exists, make sure it's associated with the current type
    // This handles cases where a user might load an existing tag under a different type
    const savedData = JSON.parse(localStorage.getItem(`qa-${tag}`));
    if (savedData && savedData.deviceType !== currentDeviceType) {
      // Prompt user or handle logic for changing device type for an existing tag
      const confirmChange = confirm(`This device (${tag}) was previously saved as a ${savedData.deviceType.toUpperCase()}. Do you want to switch it to a ${currentDeviceType.toUpperCase()}? This will reset its checklist progress for the new type.`);
      if (confirmChange) {
        // Reset and apply new device type
        const newChecklist = deviceChecklists[currentDeviceType].map(item => {
          if (item.type === 'checkbox') {
            return { type: 'checkbox', content: item.content, checked: false };
          } else {
            return { type: 'text', content: item.content };
          }
        });
        localStorage.setItem(`qa-${tag}`, JSON.stringify({
          status: "default",
          deviceType: currentDeviceType,
          checklist: newChecklist
        }));
      } else {
        // If user cancels, switch to the original device type and load it
        switchDeviceType(savedData.deviceType);
        selectDevice(tag);
        document.getElementById("serviceTagInput").value = '';
        return; // Exit to prevent re-loading under the wrong type
      }
    }
  }

  selectDevice(tag);
  document.getElementById("serviceTagInput").value = '';
}


function createDeviceButton(tag) {
  const btn = document.createElement("button");
  btn.textContent = tag;
  btn.className = "device-button";
  btn.id = `btn-${tag}`;
  btn.onclick = () => selectDevice(tag);

  const deleteX = document.createElement("span");
  deleteX.textContent = "×";
  deleteX.className = "delete-x";
  deleteX.onclick = (e) => {
    e.stopPropagation();
    localStorage.removeItem(`qa-${tag}`);
    btn.remove();
    if (currentTag === tag) { // If deleting the currently active device
      checklistEl.innerHTML = '';
      progressBar.style.width = '0%';
      progressBar.textContent = '0%';
      qaCompleteMsg.style.display = 'none';
      repairBtn.style.display = 'none';
      checkAllBtn.style.display = 'none';
      currentTag = null; // Clear current tag
    }
    updateCounters(); // Update counters after deletion
  };

  btn.appendChild(deleteX);
  deviceSelector.appendChild(btn);
  updateCounters(); // Update counters when a new device button is created
}

function selectDevice(tag) {
  currentTag = tag;
  const btn = document.getElementById(`btn-${tag}`);
  // Remove active class from all device buttons, then add to the selected one
  document.querySelectorAll('.device-button').forEach(b => b.classList.remove('active'));
  if (btn) btn.classList.add('active');

  let saved = JSON.parse(localStorage.getItem(`qa-${tag}`));

  // If no saved data, or if the saved device type doesn't match the current tab,
  // initialize with the checklist for the current device type.
  if (!saved || saved.deviceType !== currentDeviceType) {
    saved = {
      status: "default",
      deviceType: currentDeviceType, // Store the device type with the tag
      checklist: deviceChecklists[currentDeviceType].map(item => {
        if (item.type === 'checkbox') {
          return { type: 'checkbox', content: item.content, checked: false };
        } else {
          return { type: 'text', content: item.content };
        }
      })
    };
    localStorage.setItem(`qa-${tag}`, JSON.stringify(saved)); // Save the newly initialized data
  }

  checklistEl.innerHTML = '';
  saved.checklist.forEach((item, index) => {
    if (item.type === 'checkbox') {
      const li = document.createElement("li");
      const checkbox = document.createElement("input");
      checkbox.type = "checkbox";
      checkbox.checked = item.checked;
      checkbox.onchange = () => {
        saved.checklist[index].checked = checkbox.checked;
        localStorage.setItem(`qa-${tag}`, JSON.stringify(saved));
        updateProgress(saved.checklist);
      };
      li.appendChild(checkbox);
      li.appendChild(document.createTextNode(item.content));
      checklistEl.appendChild(li);
    } else if (item.type === 'text') {
      const textElement = document.createElement("p");
      textElement.textContent = item.content;
      textElement.classList.add("checklist-separator");
      checklistEl.appendChild(textElement);
    }
  });

  updateProgress(saved.checklist);
  updateButtonStatus(tag, saved.status);
  checkAllBtn.style.display = 'inline-block'; // Show check all button when a device is selected
}

// Only count actual checkboxes for progress
function updateProgress(checklist) {
  const checkboxItems = checklist.filter(item => item.type === 'checkbox');
  const total = checkboxItems.length;
  const checked = checkboxItems.filter(i => i.checked).length;
  const percent = total === 0 ? 0 : Math.round((checked / total) * 100);

  progressBar.style.width = percent + "%";
  progressBar.textContent = percent + "%";

  if (percent === 100 && total > 0) {
    repairBtn.style.display = "inline-block";
  } else {
    repairBtn.style.display = "none";
    qaCompleteMsg.style.display = "none";
  }
}

function resetChecklist() { // Now acts as "Complete"
  if (!currentTag) return;
  const saved = JSON.parse(localStorage.getItem(`qa-${currentTag}`));
  saved.status = "complete";
  localStorage.setItem(`qa-${currentTag}`, JSON.stringify(saved));
  updateButtonStatus(currentTag, "complete");
  qaCompleteMsg.style.display = "block";
  repairBtn.style.display = "none";
  updateCounters(); // Update counters after status change
}

function kickbackDevice() {
  if (!currentTag) return;
  const saved = JSON.parse(localStorage.getItem(`qa-${currentTag}`));
  saved.status = "kickback";
  localStorage.setItem(`qa-${currentTag}`, JSON.stringify(saved));
  updateButtonStatus(currentTag, "kickback");
  qaCompleteMsg.style.display = "none";
  repairBtn.style.display = "none";
  updateCounters(); // Update counters after status change
}

function updateButtonStatus(tag, status) {
  const btn = document.getElementById(`btn-${tag}`);
  if (btn) { // Ensure button exists before trying to modify
    btn.classList.remove("complete", "kickback");
    if (status === "complete") {
      btn.classList.add("complete");
    } else if (status === "kickback") {
      btn.classList.add("kickback");
    }
  }
}

// --- New function for "Check All" ---
function checkAllChecklistItems() {
  if (!currentTag) return; // No device selected
  const saved = JSON.parse(localStorage.getItem(`qa-${currentTag}`));
  saved.checklist.forEach(item => {
    if (item.type === 'checkbox') {
      item.checked = true;
    }
  });
  localStorage.setItem(`qa-${currentTag}`, JSON.stringify(saved));
  selectDevice(currentTag); // Re-render to show all checked
}

// --- New functions for counters ---
function updateCounters() {
  let passedCount = 0;
  let kickbackCount = 0;
  for (let i = 0; i < localStorage.length; i++) {
    const key = localStorage.key(i);
    if (key.startsWith("qa-")) {
      const saved = JSON.parse(localStorage.getItem(key));
      if (saved && saved.status === "complete") {
        passedCount++;
      } else if (saved && saved.status === "kickback") {
        kickbackCount++;
      }
    }
  }
  passedCounterEl.textContent = passedCount;
  kickbackCounterEl.textContent = kickbackCount;
}

// --- Modified window.onload to include new initializations ---
window.onload = function () {
  createDeviceTypeTabs(); // Create the tabs initially
  // Load buttons for ALL saved devices, regardless of type, but selectDevice will filter by type
  updateAllDeviceButtons();
  updateCounters(); // Initialize counters on load
  // Initially hide checkAllBtn
  checkAllBtn.style.display = 'none';
};

function updateAllDeviceButtons() {
  deviceSelector.innerHTML = ''; // Clear existing buttons
  for (let key in localStorage) {
    if (key.startsWith("qa-")) {
      const tag = key.slice(3);
      createDeviceButton(tag); // Recreate all buttons
      // Ensure existing buttons reflect their status
      const saved = JSON.parse(localStorage.getItem(key));
      if (saved) {
        updateButtonStatus(tag, saved.status);
      }
    }
  }
}