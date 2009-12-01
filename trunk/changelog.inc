<ul>
<li>Version 0.6b2.
   <ul>
      <li>Numerous bug fixes. Thanks for all who reported on bugs!</li>
      <li>Added Form Disposition to do an HTTP GET or POST to any arbitrary URL for form handling</li>
      <li>Added capability (and example of) advanced multipage contact form where a previous response gets displayed on a subsequent page as per uptoeleven's FR</li>

      <li>Fix for template $fld_X remapping on XML import</li>
      <li>Extended to provide DOM/CSS id attributes to fields</li>
      <li>Cleaned up bad use of references for PHP 5.x</li>
   </ul>
</li>
<li>Version 0.6b1.
	<ul>
		<li>Updated database code to be CMSMS 1.7-compatible</li>
		<li>Added Button field type.</li>
		<li>Implemented FormBrowser v0.3 Disposition.</li>
		<li>Numerous code fixes.</li>
	</ul>
</li>
<li>Version 0.5.12. 3 Sept 09. Numerous user-submitted fixes. Thanks to Calguy and Simon Schaufi.</li>
<li>Version 0.5.11. 19 Jan 09. (calguy1000)
  <ul>
    <li>Adds the capability to call a UDT before the form is displayed.</li>
    <li>Move changelog to an external file.</li>
  </ul>
</li>
<li>Version 0.5.10. 12 Jan 09. Bug fixes to restore functionality of FormBrowser.</li>
<li>Version 0.5.9. Jan 09 (calguy1000) - Minor change to the DispositionFile class.</li>
<li>Version 0.5.8. Jan 09 (calguy1000) - Minor change to the DispositionMultiselectFileDirector class.</li>
<li>Version 0.5.7. Jan 09 (calguy1000) - Minor change to the DispositionMultiselectFileDirector class.</li>
<li>Version 0.5.6. 23 Dec 08. Bug fixes.</li>
<li>Version 0.5.5. 1 Oct 08. Bug fixes. Added new DispositionFromEmailAddressField to replace deficiencies with Email Address 'From' Field type.</li>
<li>Version 0.5.4. 29 Aug 08. Scrapping plans for 0.6. Bug fixes. Ted's new \"Email \"From Address\" Again Field\". Added database indices.</li>
<li>Version 0.5.3. 26 May 08. Bugfixes to previous set of bugfixes. Now ready for major upgrades for 0.6.</li>
<li>Version 0.5.2. 25 May 08. Bugfixes. Release in preparation for substantial reworking for 0.6</li>
<li>Version 0.5.1. 30 April 08. Bugfixes, rewrote some of the File Upload code to prevent white screen of annoyance.</li>
<li>Version 0.5. 19 April 08. Bugfixes, support for FormBrowser fixes and features, disposition type for upcoming FormBrowser version.</li>
<li>Version 0.4.4. 26 Sept 07. Bugfixes for radio button groups, fix for validation of DispositionDeliverToEmailAddressField.</li>
<li>Version 0.4.3. 18 Sept 07. Bugfixes for pulldowns using numbers, option to inline form, fixes for table-layouts provided by Ap Muthu, fix for requirability of Dispositions.</li>
<li>Version 0.4.2. 14 Sept 07. Bugfixes for XML export, RadioButton Group submission, and HTML labels.</li>
<li>Version 0.4.1. Bugfixes for File Disposition Types.</li>
<li>Version 0.4
	<ul>
		<li>Upped minimum CMS version to 1.1</li>
		<li>Numerous Bug fixes; too many to enumerate here</li>
		<li>Massive rejiggering of code to work with CMS MS 1.1 parameter sanitizing regime</li>
		<li>Added field types: \"TextField (Multiple)\" and \"Computed Field\"</li>
		<li>Added dispositions: \"Save Results to File Based on Pulldown\" and \"Save Results to File(s) Based on Multiple Selections\"(Calguy)</li>
		<li>Added neat-o AJAX-y template-copying for Form Templates, gave versions of old table-based templates to soothe the raging masses.</li>
		<li>Added ability to pass default values to form elements from module tag using value_FIELDNAME=\"value\" syntax (Calguy)</li>
		<li>Added option to display field ids in admin (Calguy)</li>
	</ul>
</li>
<li>Version 0.3
   <ul>
      <li>Added Captcha module support.</li>
      <li>Reworked labeling of form elements to be XHTML compliant</li>
      <li>Added Paul Noone's new, improved contact form templates</li>
      <li>Implementation of Email Subject Field</li>
      <li>Added admin-specifiable values to hidden field type, which may contain parsable smarty tags</li>
      <li>Text Area Field now allows specification of rows and cols in the admin, instead of CSS</li>
      <li>Added Drag'n'Drop reordering of form fields.</li>
      <li>Added non-blank field name and unique field name global configuration options and validation</li>
      <li>Implemented templated display of form results upon submission in addition to page redirecting</li>
      <li></li>
   </ul>
</li>
<li>Version 0.2.4 - 09 May 2007
   <p>Big release:<ul>
   <li>Added \"Email Form to User-Supplied Address\" disposition.</li>
   <li>Added \"TimePicker\" field type. Split \"Link\" field into a \"User-supplied Link\" field and a \"Static Link\" field.</li>
   <li>Added HTML email option and auto-templates for all email dispositions.</li>
   <li>Added primitive anti-spam features.</li>
   <li>Added XML-based import and export of forms.</li>
   </ul>
   </p>
</li>
<li>Version 0.2.3
   <p>Add the ability to call a selectable user defined tag upon disposition of the form.</p>
</li>
<li>Version 0.2.2
   <p>Many thanks to Utter Design for sponsoring some changes and bugfixes, which include:
   <ul>
      <li>Added file upload capabilities to the email disposition</li>
      <li>Add PageSetStart and PageSetEnd field types
      <p>These field types allow you to create fieldsets in your form, and organise
      your fields logically for the user.  You can even nest fieldsets, and
      associate a style with them appropriately.  The default template was changed
      to prevent it from wrapping a div around these types of fields.
      </p>
      </li>
      <li>Fixed a problem with DATETIME fields on install
      <p>This solves a problem when on install the response table would sometimes not get created when using adodb lite</li>
      </li>
   </ul>
   </p>
</li>
<li>Version 0.2 - 14 November 2006. Calguy &amp; tsw's bug fixes for field re-ordering and custom templates. Additional bug fixes for field labels.</li>
<li>Version 0.1 - 8 July 2006. Initial Release</li>
</ul>
