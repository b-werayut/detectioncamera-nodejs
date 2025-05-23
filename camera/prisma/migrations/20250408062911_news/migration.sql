BEGIN TRY

BEGIN TRAN;

-- CreateTable
CREATE TABLE [dbo].[TmstCameraDetectionLogs] (
    [id] INT NOT NULL IDENTITY(1,1),
    [foldername] NVARCHAR(1000),
    [linesendstatus] BIT NOT NULL CONSTRAINT [TmstCameraDetectionLogs_linesendstatus_df] DEFAULT 0,
    [picstatus] BIT NOT NULL CONSTRAINT [TmstCameraDetectionLogs_picstatus_df] DEFAULT 0,
    [vdostatus] BIT NOT NULL CONSTRAINT [TmstCameraDetectionLogs_vdostatus_df] DEFAULT 0,
    [createdAt] DATETIME2 NOT NULL CONSTRAINT [TmstCameraDetectionLogs_createdAt_df] DEFAULT CURRENT_TIMESTAMP,
    [updatedAt] DATETIME2 NOT NULL,
    CONSTRAINT [TmstCameraDetectionLogs_pkey] PRIMARY KEY CLUSTERED ([id])
);

-- CreateTable
CREATE TABLE [dbo].[TmstPowerDetectionLogs] (
    [id] INT NOT NULL IDENTITY(1,1),
    [point] NVARCHAR(1000),
    [status] NVARCHAR(1000),
    [value] INT,
    [timestamp] DATETIME2,
    [createdAt] DATETIME2 NOT NULL CONSTRAINT [TmstPowerDetectionLogs_createdAt_df] DEFAULT CURRENT_TIMESTAMP,
    [updatedAt] DATETIME2 NOT NULL,
    CONSTRAINT [TmstPowerDetectionLogs_pkey] PRIMARY KEY CLUSTERED ([id])
);

COMMIT TRAN;

END TRY
BEGIN CATCH

IF @@TRANCOUNT > 0
BEGIN
    ROLLBACK TRAN;
END;
THROW

END CATCH
