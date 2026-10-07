-- Korvia offer letter design for offer_letter_templates (id = 1).
-- Run in phpMyAdmin with the app database selected.

UPDATE offer_letter_templates SET content = '<div class="ol-letterhead" style="text-align: center; font-family: ''DejaVu Sans'', Arial, sans-serif;">
<div style="font-family: ''DejaVu Serif'', Georgia, ''Times New Roman'', serif; font-size: 19pt; font-weight: bold; color: #7f9c8e; letter-spacing: 1px;">KORVIA RETAIL PRIVATE LIMITED</div>
<div style="font-size: 7.5pt; color: #9aa6a0; letter-spacing: 0.5px; margin-top: 2px;">KORVIA SMART &bull; Surat, Gujarat</div>
<div style="border-top: 1px solid #c9a44c; margin: 8px 0 6px 0;">&nbsp;</div>
</div>

<div style="font-family: ''DejaVu Sans'', Arial, sans-serif; font-size: 8.6pt; line-height: 1.45; color: #222222;">
<table style="width: 100%; margin: 0 0 10px 0; border-collapse: collapse;">
<tbody>
<tr>
<td style="width: 50%; text-align: left;"><b>Ref:</b> <i style="color: #666666;">KRPL/HR/{{ref_number}}</i></td>
<td style="width: 50%; text-align: right;"><b>Date:</b> <i style="color: #666666;">{{letter_date}}</i></td>
</tr>
</tbody>
</table>

<p style="margin: 0;">To,</p>
<p style="margin: 0;"><b>{{candidate_name}}</b></p>
<p style="margin: 0 0 12px 0; color: #666666;">{{candidate_address}}</p>

<p style="margin: 0 0 6px 0;"><b>Subject: <span style="color: #1f4e3d;">Offer of Employment - {{designation}}</span></b></p>

<p style="text-align: center; margin: 4px 0 8px 0; font-family: ''DejaVu Serif'', Georgia, ''Times New Roman'', serif; font-size: 13pt; font-weight: bold; color: #1f4e3d; letter-spacing: 1px;">OFFER LETTER</p>

<p style="text-align: justify; margin: 0 0 10px 0;">Dear {{candidate_name}}, Further to your application and the interview process, we are pleased to offer you employment with Korvia Retail Private Limited (&ldquo;the Company&rdquo;) on the terms outlined below. This offer is provisional and shall stand confirmed only upon issuance of a formal Letter of Appointment on your date of joining.</p>

<p style="margin: 8px 0 4px 0; padding-bottom: 3px; border-bottom: 1px solid #1f4e3d; font-weight: bold; font-size: 9pt; color: #1f4e3d;">POSITION OFFERED</p>
<table style="width: 100%; margin: 0 0 8px 0; border-collapse: collapse;">
<tbody>
<tr>
<td style="width: 18%; padding: 5px 4px; border-bottom: 1px solid #dddddd; font-weight: bold; color: #1f4e3d;">Designation</td>
<td style="width: 30%; padding: 5px 4px; border-bottom: 1px solid #dddddd;">{{designation}}</td>
<td style="width: 27%; padding: 5px 4px; border-bottom: 1px solid #dddddd; font-weight: bold; color: #1f4e3d;">Work Location</td>
<td style="width: 25%; padding: 5px 4px; border-bottom: 1px solid #dddddd;">{{work_location}}</td>
</tr>
<tr>
<td style="padding: 5px 4px; border-bottom: 1px solid #dddddd; font-weight: bold; color: #1f4e3d;">Department</td>
<td style="padding: 5px 4px; border-bottom: 1px solid #dddddd;">{{department_name}}</td>
<td style="padding: 5px 4px; border-bottom: 1px solid #dddddd; font-weight: bold; color: #1f4e3d;">Employment Type</td>
<td style="padding: 5px 4px; border-bottom: 1px solid #dddddd;">{{employment_type}}</td>
</tr>
<tr>
<td style="padding: 5px 4px; border-bottom: 1px solid #dddddd; font-weight: bold; color: #1f4e3d;">Reporting To</td>
<td style="padding: 5px 4px; border-bottom: 1px solid #dddddd;">{{reporting_to}}</td>
<td style="padding: 5px 4px; border-bottom: 1px solid #dddddd; font-weight: bold; color: #1f4e3d;">Proposed Date of Joining</td>
<td style="padding: 5px 4px; border-bottom: 1px solid #dddddd;">{{joining_date_dmy}}</td>
</tr>
</tbody>
</table>

<p style="margin: 10px 0 4px 0; padding-bottom: 3px; border-bottom: 1px solid #c9a44c; font-weight: bold; font-size: 9pt; color: #1f4e3d;">COMPENSATION</p>
<p style="text-align: justify; margin: 0 0 8px 0;">The offered Annual CTC (Cost to Company) is &#8377; {{annual_ctc}} per annum. A detailed monthly salary structure (Basic, HRA, Other Allowance and applicable statutory deductions) will be shared as Annexure A along with your Letter of Appointment on the date of joining.</p>

<p style="margin: 10px 0 4px 0; padding-bottom: 3px; border-bottom: 1px solid #c9a44c; font-weight: bold; font-size: 9pt; color: #1f4e3d;">OFFER VALIDITY &amp; ACCEPTANCE</p>
<p style="text-align: justify; margin: 0 0 8px 0;">This offer is valid until {{offer_valid_until}}. Kindly confirm your acceptance by signing and returning a copy of this letter within the stated period, failing which the Company reserves the right to withdraw this offer.</p>

<p style="margin: 10px 0 4px 0; padding-bottom: 3px; border-bottom: 1px solid #c9a44c; font-weight: bold; font-size: 9pt; color: #1f4e3d;">CONDITIONS OF OFFER</p>
<p style="text-align: justify; margin: 0 0 8px 0;">This offer is subject to (a) satisfactory verification of documents, credentials and references provided by you, (b) submission of original documents listed overleaf on or before your date of joining, and (c) your continued suitability for the role. Any material misrepresentation may result in withdrawal of this offer at any stage.</p>

<p style="margin: 10px 0 4px 0; padding-bottom: 3px; border-bottom: 1px solid #c9a44c; font-weight: bold; font-size: 9pt; color: #1f4e3d;">DOCUMENTS REQUIRED ON JOINING</p>
<p style="text-align: justify; margin: 0 0 8px 0;">Photo ID proof (Aadhaar/PAN), address proof, latest passport-size photographs, educational certificates, relieving letter and last salary slip from previous employer (if applicable), and bank account details for payroll processing.</p>

<p style="text-align: justify; margin: 0 0 8px 0;">We look forward to welcoming you to the Korvia Smart family. Please reach out to HR for any clarification regarding this offer.</p>

<table style="width: 100%; margin: 22px 0 0 0; border-collapse: collapse;">
<tbody>
<tr>
<td style="width: 50%; font-weight: bold; font-size: 8pt; color: #1f4e3d;">For KORVIA RETAIL PRIVATE LIMITED</td>
<td style="width: 50%; font-weight: bold; font-size: 8pt; color: #1f4e3d;">CANDIDATE ACCEPTANCE</td>
</tr>
<tr>
<td style="height: 60px; vertical-align: bottom;">{{signature_and_stamp}}</td>
<td style="height: 60px; vertical-align: bottom;">&nbsp;</td>
</tr>
<tr>
<td style="padding-top: 2px;">
<div style="border-top: 1px solid #333333; width: 45%;">&nbsp;</div>
<div style="font-weight: bold; margin-top: -8px;">Authorised Signatory</div>
</td>
<td style="padding-top: 2px;">
<div style="border-top: 1px solid #333333; width: 45%;">&nbsp;</div>
<div style="font-weight: bold; margin-top: -8px;">Signature &amp; Date</div>
</td>
</tr>
</tbody>
</table>
</div>', content_page2 = NULL, updated_at = NOW() WHERE id = 1;

UPDATE offer_letter_templates SET content_pages = JSON_ARRAY(content) WHERE id = 1;
